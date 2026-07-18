<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Moderator;

/**
 * Input for `ModeratorsResource::delete()` (`DELETE /moderators`). Either
 * `mod_id` or `user_id` is required to identify the moderator to delete.
 */
final class DeleteModeratorRequest
{
    public function __construct(
        public readonly ?int $mod_id = null,
        public readonly ?int $user_id = null,
    ) {
    }
}
