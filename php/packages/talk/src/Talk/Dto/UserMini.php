<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto;

final class UserMini
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $username,
        public readonly ?string $picture_url,
    ) {
    }
}
