<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Http;

use Hyvor\Sdk\Auth\TokenProviderInterface;
use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Exceptions\NetworkException;
use Hyvor\Sdk\RequestOptions;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Executes API requests: builds the HTTP request, authenticates it, retries
 * on transient failures with exponential backoff, and maps error responses
 * to typed exceptions.
 *
 * @internal
 */
final class Transport
{
    private const BASE_RETRY_DELAY_MS = 200;

    public function __construct(
        private readonly ClientInterface $httpClient,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly LoggerInterface $logger,
        private readonly TokenProviderInterface $tokenProvider,
        private readonly string $baseUrl,
        private readonly int $defaultRetryMaxAttempts,
        private readonly float $defaultRetryBackoffFactor,
        private readonly string $userAgent,
    ) {
    }

    /**
     * @param array<mixed>|null $jsonBody
     * @return array<mixed>
     *
     * @throws HyvorApiException
     */
    public function request(string $method, string $path, ?array $jsonBody = null, ?RequestOptions $options = null): array
    {
        $maxAttempts = max(1, $options?->retryMaxAttempts ?? $this->defaultRetryMaxAttempts);
        $backoffFactor = $options?->retryBackoffFactor ?? $this->defaultRetryBackoffFactor;

        $lastException = null;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                $response = $this->send($method, $path, $jsonBody, $options);
            } catch (ClientExceptionInterface $e) {
                $lastException = new NetworkException($e->getMessage(), $e);

                if ($attempt < $maxAttempts) {
                    $this->waitBeforeRetry($attempt, $backoffFactor, null, $e->getMessage());
                    continue;
                }

                throw $lastException;
            }

            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                return self::decodeSuccessBody($response);
            }

            $exception = ErrorMapper::fromResponse($response);

            if (ErrorMapper::isRetryable($response->getStatusCode()) && $attempt < $maxAttempts) {
                $lastException = $exception;
                $this->waitBeforeRetry($attempt, $backoffFactor, $response, $exception->getMessage());
                continue;
            }

            throw $exception;
        }

        throw $lastException ?? new NetworkException('Request failed after retries.');
    }

    /**
     * @param array<mixed>|null $jsonBody
     */
    private function send(string $method, string $path, ?array $jsonBody, ?RequestOptions $options): ResponseInterface
    {
        $request = $this->requestFactory
            ->createRequest($method, $this->baseUrl . $path)
            ->withHeader('Authorization', 'Bearer ' . $this->tokenProvider->getToken())
            ->withHeader('Accept', 'application/json')
            ->withHeader('User-Agent', $this->userAgent);

        if ($jsonBody !== null) {
            $request = $request
                ->withHeader('Content-Type', 'application/json')
                ->withBody($this->streamFactory->createStream(json_encode($jsonBody, JSON_THROW_ON_ERROR)));
        }

        if ($options?->idempotencyKey !== null) {
            $request = $request->withHeader('Idempotency-Key', $options->idempotencyKey);
        }

        $this->logger->debug('Hyvor SDK: sending request', ['method' => $method, 'url' => (string) $request->getUri()]);

        $response = $this->httpClient->sendRequest($request);

        $this->logger->debug('Hyvor SDK: received response', [
            'method' => $method,
            'url' => (string) $request->getUri(),
            'status' => $response->getStatusCode(),
        ]);

        return $response;
    }

    /**
     * @return array<mixed>
     */
    private static function decodeSuccessBody(ResponseInterface $response): array
    {
        $body = (string) $response->getBody();

        if ($body === '') {
            return [];
        }

        /** @var array<mixed> $decoded */
        $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        return $decoded;
    }

    private function waitBeforeRetry(int $attempt, float $backoffFactor, ?ResponseInterface $response, string $reason): void
    {
        $delayMs = $this->computeBackoffDelayMs($attempt, $backoffFactor, $response);

        $this->logger->warning('Hyvor SDK: retrying request', [
            'attempt' => $attempt,
            'delayMs' => $delayMs,
            'reason' => $reason,
        ]);

        usleep($delayMs * 1000);
    }

    private function computeBackoffDelayMs(int $attempt, float $backoffFactor, ?ResponseInterface $response): int
    {
        $retryAfter = $response?->getHeaderLine('retry-after');
        if ($retryAfter !== null && $retryAfter !== '' && is_numeric($retryAfter)) {
            return (int) $retryAfter * 1000;
        }

        return (int) (self::BASE_RETRY_DELAY_MS * ($backoffFactor ** ($attempt - 1)));
    }
}
