<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\User;

final class User
{
    public function __construct(
        public readonly int $id,
        public readonly UserRole $role,
        public readonly int $created_at,
        public readonly UserMini $user,
    ) {
    }
}
