<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Comment;

use Hyvor\Sdk\Talk\Dto\Page\Page;
use Hyvor\Sdk\Talk\Dto\User\CommentingUser;

final class Comment
{
    public function __construct(
        public readonly int $id,
        public readonly int $created_at,

        public readonly CommentingUser $user,
        public readonly Page $page,

        public readonly string $body,
        public readonly string $body_html,

        public readonly CommentStatus $status,

        public readonly ?Comment $parent,
        public readonly ?string $ip_address,

        public readonly bool $has_mod_seen,
        public readonly bool $has_mod_replied,
        public readonly bool $is_featured,
        public readonly bool $is_loved,
        public readonly bool $is_edited,

        public readonly bool $has_questions,
        public readonly bool $has_links,
        public readonly bool $has_media,
        public readonly bool $is_automatic_spam,

        public readonly int $flags_count,
        public readonly ?int $last_flag_at,

        public readonly int $upvotes,
        public readonly int $downvotes,

        public readonly ?VoteDirection $user_vote,

        /** @var CommentHistory[] */
        public readonly array $history,
    ) {
    }
}
