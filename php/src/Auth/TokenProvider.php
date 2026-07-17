<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Auth;

use Hyvor\Sdk\Exceptions\AuthenticationException;
use Hyvor\Sdk\Http\HttpClientInterface;
use Hyvor\Sdk\Http\HttpRequest;
use Hyvor\Sdk\Http\HttpTransportException;
use Hyvor\Sdk\Version;
use Psr\Log\LoggerInterface;

/**
 * Resolves the bearer token used to authenticate requests.
 *
 * If a static JWT token was configured, it is used as-is. Otherwise, a
 * short-lived JWT is exchanged for the configured cloud API key and cached
 * until shortly before it expires.
 *
 * @internal
 */
final class TokenProvider
{
    private const TOKEN_EXCHANGE_PATH = '/api/cloud/token';
    private const EXPIRY_LEEWAY_SECONDS = 30;

    private ?string $cachedToken = null;
    private ?int $cachedTokenExpiresAt = null;

    public function __construct(
        private readonly ?string $cloudApiKey,
        private readonly ?string $staticJwtToken,
        private readonly string $baseUrl,
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function getToken(): string
    {
        if ($this->staticJwtToken !== null) {
            return $this->staticJwtToken;
        }

        if ($this->cachedToken !== null && $this->cachedTokenExpiresAt > time() + self::EXPIRY_LEEWAY_SECONDS) {
            return $this->cachedToken;
        }

        return $this->exchangeToken();
    }

    private function exchangeToken(): string
    {
        $this->logger->debug('Hyvor SDK: exchanging cloud API key for a JWT token.');

        $request = new HttpRequest(
            method: 'POST',
            url: $this->baseUrl . self::TOKEN_EXCHANGE_PATH,
            headers: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'User-Agent' => 'hyvor/sdk-php/' . Version::VERSION,
            ],
            body: json_encode(['api_key' => $this->cloudApiKey], JSON_THROW_ON_ERROR),
        );

        try {
            $response = $this->httpClient->sendRequest($request);
        } catch (HttpTransportException $e) {
            throw new AuthenticationException(
                'Failed to exchange the cloud API key for a JWT token: ' . $e->getMessage(),
                null,
                null,
                $e,
            );
        }

        if ($response->statusCode < 200 || $response->statusCode >= 300) {
            throw new AuthenticationException(
                "Failed to exchange the cloud API key for a JWT token (HTTP {$response->statusCode}).",
                $response->statusCode,
            );
        }

        $data = $response->json();
        $token = $data['token'] ?? null;
        $expiresIn = $data['expires_in'] ?? null;

        if (!is_string($token) || $token === '') {
            throw new AuthenticationException('Token exchange response did not include a token.');
        }

        $this->cachedToken = $token;
        $this->cachedTokenExpiresAt = time() + (is_int($expiresIn) ? $expiresIn : 3600);

        return $token;
    }
}
