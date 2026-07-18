<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\User;

/**
 * Input for `InvitesResource::create()` (`POST /invites`). Either `$username`
 * or `$email` of the invitee's HYVOR account is required. The invitee must
 * already have a HYVOR account before being invited.
 */
final class CreateInviteRequest
{
    public function __construct(
        public readonly ?string $username = null,
        public readonly ?string $email = null,
    ) {
    }
}
