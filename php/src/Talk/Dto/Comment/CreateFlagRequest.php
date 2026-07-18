<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Comment;

/**
 * Input for `CommentsResource::createFlag()` (`POST /comment/{id}/flag`).
 * If `user_sso_id` is null, the current Console API user is used.
 */
final class CreateFlagRequest
{
    public function __construct(
        public readonly ?string $reason = null,
        public readonly ?string $user_sso_id = null,
    ) {
    }
}
