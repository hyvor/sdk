<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto;

final class ListObject
{
    public function __construct(
        public readonly int $id,
        public readonly int $created_at,
        public readonly string $name,
        public readonly ?string $description,
        public readonly int $subscribers_count,
    ) {
    }
}
