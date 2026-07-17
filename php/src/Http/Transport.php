<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Http;

use Hyvor\Sdk\Auth\TokenProvider;
use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Exceptions\NetworkException;
use Hyvor\Sdk\RequestOptions;
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
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
        private readonly TokenProvider $tokenProvider,
        private readonly string $baseUrl,
        private readonly int $defaultConnectTimeoutMs,
        private readonly int $defaultRequestTimeoutMs,
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
            } catch (HttpTransportException $e) {
                $lastException = new NetworkException($e->getMessage(), $e);

                if ($attempt < $maxAttempts) {
                    $this->waitBeforeRetry($attempt, $backoffFactor, null, $e->getMessage());
                    continue;
                }

                throw $lastException;
            }

            if ($response->statusCode >= 200 && $response->statusCode < 300) {
                return $response->body === '' ? [] : $response->json();
            }

            $exception = ErrorMapper::fromResponse($response);

            if (ErrorMapper::isRetryable($response->statusCode) && $attempt < $maxAttempts) {
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
    private function send(string $method, string $path, ?array $jsonBody, ?RequestOptions $options): HttpResponse
    {
        $headers = [
            'Authorization' => 'Bearer ' . $this->tokenProvider->getToken(),
            'Accept' => 'application/json',
            'User-Agent' => $this->userAgent,
        ];

        $body = null;
        if ($jsonBody !== null) {
            $headers['Content-Type'] = 'application/json';
            $body = json_encode($jsonBody, JSON_THROW_ON_ERROR);
        }

        if ($options?->idempotencyKey !== null) {
            $headers['Idempotency-Key'] = $options->idempotencyKey;
        }

        $request = new HttpRequest(
            method: $method,
            url: $this->baseUrl . $path,
            headers: $headers,
            body: $body,
            connectTimeoutMs: $options?->connectTimeoutMs ?? $this->defaultConnectTimeoutMs,
            requestTimeoutMs: $options?->requestTimeoutMs ?? $this->defaultRequestTimeoutMs,
        );

        $this->logger->debug('Hyvor SDK: sending request', ['method' => $method, 'url' => $request->url]);

        $response = $this->httpClient->sendRequest($request);

        $this->logger->debug('Hyvor SDK: received response', [
            'method' => $method,
            'url' => $request->url,
            'status' => $response->statusCode,
        ]);

        return $response;
    }

    private function waitBeforeRetry(int $attempt, float $backoffFactor, ?HttpResponse $response, string $reason): void
    {
        $delayMs = $this->computeBackoffDelayMs($attempt, $backoffFactor, $response);

        $this->logger->warning('Hyvor SDK: retrying request', [
            'attempt' => $attempt,
            'delayMs' => $delayMs,
            'reason' => $reason,
        ]);

        usleep($delayMs * 1000);
    }

    private function computeBackoffDelayMs(int $attempt, float $backoffFactor, ?HttpResponse $response): int
    {
        $retryAfter = $response?->header('retry-after');
        if ($retryAfter !== null && is_numeric($retryAfter)) {
            return (int) $retryAfter * 1000;
        }

        return (int) (self::BASE_RETRY_DELAY_MS * ($backoffFactor ** ($attempt - 1)));
    }
}
