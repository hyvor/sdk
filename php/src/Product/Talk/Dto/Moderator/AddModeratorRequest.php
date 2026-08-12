<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Dto\Moderator;

use Hyvor\Sdk\Product\Talk\Dto\User\UserRole;

final class AddModeratorRequest
{
    public function __construct(
        public readonly int $user_id,
        public readonly UserRole $role = UserRole::MOD,
        public readonly ?ModeratorOnDuplicate $on_duplicate = null,
    ) {
    }
}
