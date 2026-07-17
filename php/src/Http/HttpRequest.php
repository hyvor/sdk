<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Http;

final class HttpRequest
{
    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        public readonly string $method,
        public readonly string $url,
        public readonly array $headers = [],
        public readonly ?string $body = null,
        public readonly int $connectTimeoutMs = 5000,
        public readonly int $requestTimeoutMs = 10000,
    ) {
    }
}
