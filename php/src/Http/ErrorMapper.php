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

/**
 * @internal
 */
final class ErrorMapper
{
    public static function fromResponse(HttpResponse $response): HyvorApiException
    {
        $body = null;

        try {
            $decoded = $response->json();
            $body = $decoded === [] ? null : $decoded;
        } catch (\JsonException) {
            $body = null;
        }

        $message = is_array($body) && is_string($body['message'] ?? null)
            ? $body['message']
            : "Hyvor API request failed with status {$response->statusCode}";

        return match (true) {
            $response->statusCode === 422 => new ValidationFailedException(
                $message,
                is_array($body['errors'] ?? null) ? $body['errors'] : [],
                $body,
            ),
            $response->statusCode === 429 => new RateLimitException(
                $message,
                self::retryAfterSeconds($response),
                $body,
            ),
            $response->statusCode === 401 || $response->statusCode === 403 => new AuthenticationException(
                $message,
                $response->statusCode,
                $body,
            ),
            $response->statusCode === 404 => new NotFoundException($message, 404, $body),
            $response->statusCode >= 500 => new ServerErrorException($message, $response->statusCode, $body),
            default => new ApiException($message, $response->statusCode, $body),
        };
    }

    public static function isRetryable(int $statusCode): bool
    {
        return $statusCode === 429 || $statusCode >= 500;
    }

    private static function retryAfterSeconds(HttpResponse $response): ?int
    {
        $header = $response->header('retry-after');

        return $header !== null && is_numeric($header) ? (int) $header : null;
    }
}
