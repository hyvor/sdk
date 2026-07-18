<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\User;

final class LoggedInUser implements CommentingUser
{
    public function __construct(
        public readonly UserType $type,
        public readonly int $id,
        public readonly string $htid,
        public readonly string $sso_id,
        public readonly string $name,
        public readonly ?string $title,
        public readonly ?string $username,
        public readonly ?string $email,
        public readonly ?string $picture_url,
        public readonly ?string $bio,
        public readonly ?string $location,
        public readonly ?string $website_url,

        public readonly ?int $created_at,
        public readonly ?int $last_commented_at,
        public readonly int $comments_count,
        public readonly UserState $state,
        public readonly ?int $state_ends_at,
        public readonly ?string $note,
        public readonly ?string $ban_reason,
        public readonly int $reputation,
        /** @var int[] */
        public readonly array $badge_ids,
        public readonly ?string $register_ip,
        public readonly ?string $last_ip,
        public readonly int $days_visited,
        public readonly ?int $last_seen_at,
        public readonly int $emails_sent,
        public readonly ?int $last_emailed_at,
        public readonly ?UserRole $role,
    ) {
    }
}
