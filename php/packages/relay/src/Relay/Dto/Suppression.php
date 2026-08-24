<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Relay\Dto;

final class Suppression
{
    public function __construct(
        public readonly int $id,
        public readonly int $created_at,
        public readonly string $email,
        public readonly string $reason,
        public readonly ?string $description,
    ) {
    }
}
