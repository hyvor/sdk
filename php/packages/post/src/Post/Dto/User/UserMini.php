<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\User;

final class UserMini
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $username,
        public readonly ?string $picture_url,
    ) {
    }
}
