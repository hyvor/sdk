<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\User;

final class UpdateUserRequest
{
    /**
     * @param int[]|null $badge_ids Max 3.
     */
    public function __construct(
        public readonly ?UserState $state = null,
        public readonly ?int $state_ends_at = null,
        public readonly ?string $note = null,
        public readonly ?string $ban_reason = null,
        public readonly ?array $badge_ids = null,
        public readonly ?int $reputation = null,
    ) {
    }
}
