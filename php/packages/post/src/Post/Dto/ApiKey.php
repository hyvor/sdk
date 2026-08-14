<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto;

final class ApiKey
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        /** @var string[]|array<string, mixed> */
        public readonly array $scopes,
        public readonly ?string $key,
        public readonly int $created_at,
        public readonly bool $is_enabled,
        public readonly ?int $last_accessed_at,
    ) {
    }
}
