<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Moderator;

use Hyvor\Sdk\Talk\Dto\User\UserMini;
use Hyvor\Sdk\Talk\Dto\User\UserRole;

final class Mod
{
    public function __construct(
        public readonly int $id,
        public readonly int $created_at,
        public readonly UserRole $role,
        public readonly UserMini $user,
        public readonly ?UserMini $sso_user,
        public readonly bool $is_alias_used,
    ) {
    }
}
