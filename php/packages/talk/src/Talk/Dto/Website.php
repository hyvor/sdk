<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto;

final class Website
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $created_at,
        public readonly bool $is_blocked,
        public readonly bool $is_deleted,
        /** @var array<string, mixed>|null */
        public readonly ?array $metadata,
        public readonly ?string $created_by_source,
        /** @var Domain[] */
        public readonly array $domains,
    ) {
    }
}
