<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto;

final class Mod
{
    public function __construct(
        public readonly int $id,
        public readonly int $created_at,
        public readonly string $role,
        public readonly int $website_id,
        public readonly UserMini $user,
    ) {
    }
}
