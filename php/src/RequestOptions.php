<?php

declare(strict_types=1);

namespace Hyvor\Sdk;

/**
 * Per-request overrides for retries and idempotency. Timeouts are configured
 * on the PSR-18 HTTP client passed to HyvorClient, not here.
 * Any field left null falls back to the client's default configuration.
 */
final class RequestOptions
{
    public function __construct(
        public readonly ?int $retryMaxAttempts = null,
        public readonly ?float $retryBackoffFactor = null,
    ) {
    }
}
