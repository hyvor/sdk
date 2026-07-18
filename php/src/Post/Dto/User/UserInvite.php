<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\User;

final class UserInvite
{
    public function __construct(
        public readonly int $id,
        public readonly int $created_at,
        public readonly UserRole $role,
        public readonly UserMini $user,
        public readonly int $expires_at,
    ) {
    }
}
