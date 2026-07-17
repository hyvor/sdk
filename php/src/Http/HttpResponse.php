<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Http;

final class HttpResponse
{
    /**
     * @param array<string, string> $headers Header names are stored lowercase.
     */
    public function __construct(
        public readonly int $statusCode,
        public readonly array $headers,
        public readonly string $body,
    ) {
    }

    public function header(string $name): ?string
    {
        return $this->headers[strtolower($name)] ?? null;
    }

    /**
     * @return array<mixed>
     */
    public function json(): array
    {
        if ($this->body === '') {
            return [];
        }

        /** @var array<mixed> $decoded */
        $decoded = json_decode($this->body, true, 512, JSON_THROW_ON_ERROR);

        return $decoded;
    }
}
