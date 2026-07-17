<?php

declare(strict_types=1);

namespace Hyvor\Sdk;

/**
 * Per-request overrides for timeouts, retries, and idempotency.
 * Any field left null falls back to the client's default configuration.
 */
final class RequestOptions
{
    public function __construct(
        public readonly ?int $connectTimeoutMs = null,
        public readonly ?int $requestTimeoutMs = null,
        public readonly ?int $retryMaxAttempts = null,
        public readonly ?float $retryBackoffFactor = null,
        public readonly ?string $idempotencyKey = null,
    ) {
    }
}
