<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Moderator;

use Hyvor\Sdk\Talk\Dto\User\UserRole;

final class AddModeratorRequest
{
    public function __construct(
        public readonly int $user_id,
        public readonly UserRole $role = UserRole::MOD,
        public readonly ?ModeratorOnDuplicate $on_duplicate = null,
    ) {
    }
}
