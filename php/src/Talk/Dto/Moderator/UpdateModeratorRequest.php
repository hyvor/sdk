<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Moderator;

use Hyvor\Sdk\Talk\Dto\User\UserRole;

/**
 * Input for `ModeratorsResource::update()` (`PATCH /moderators`). Either
 * `mod_id` or `user_id` is required to identify the moderator to update.
 * The owner's role cannot be changed.
 */
final class UpdateModeratorRequest
{
    public function __construct(
        public readonly ?int $mod_id = null,
        public readonly ?int $user_id = null,
        public readonly ?UserRole $role = null,
        public readonly ?string $sso_user_htid = null,
        public readonly ?bool $is_alias_used = null,
    ) {
    }
}
