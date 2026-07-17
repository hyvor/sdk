<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Http;

use Hyvor\Sdk\Exceptions\ApiException;
use Hyvor\Sdk\Exceptions\AuthenticationException;
use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Exceptions\NotFoundException;
use Hyvor\Sdk\Exceptions\RateLimitException;
use Hyvor\Sdk\Exceptions\ServerErrorException;
use Hyvor\Sdk\Exceptions\ValidationFailedException;
use Psr\Http\Message\ResponseInterface;

/**
 * @internal
 */
final class ErrorMapper
{
    public static function fromResponse(ResponseInterface $response): HyvorApiException
    {
        $statusCode = $response->getStatusCode();
        $body = self::decodeBody($response);

        $message = is_array($body) && is_string($body['message'] ?? null)
            ? $body['message']
            : "Hyvor API request failed with status {$statusCode}";

        return match (true) {
            $statusCode === 422 => new ValidationFailedException(
                $message,
                is_array($body['errors'] ?? null) ? $body['errors'] : [],
                $body,
            ),
            $statusCode === 429 => new RateLimitException(
                $message,
                self::retryAfterSeconds($response),
                $body,
            ),
            $statusCode === 401 || $statusCode === 403 => new AuthenticationException(
                $message,
                $statusCode,
                $body,
            ),
            $statusCode === 404 => new NotFoundException($message, 404, $body),
            $statusCode >= 500 => new ServerErrorException($message, $statusCode, $body),
            default => new ApiException($message, $statusCode, $body),
        };
    }

    public static function isRetryable(int $statusCode): bool
    {
        return $statusCode === 429 || $statusCode >= 500;
    }

    /**
     * @return array<mixed>|null
     */
    private static function decodeBody(ResponseInterface $response): ?array
    {
        $raw = (string) $response->getBody();

        if ($raw === '') {
            return null;
        }

        try {
            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }

        return is_array($decoded) ? $decoded : null;
    }

    private static function retryAfterSeconds(ResponseInterface $response): ?int
    {
        $header = $response->getHeaderLine('retry-after');

        return $header !== '' && is_numeric($header) ? (int) $header : null;
    }
}
