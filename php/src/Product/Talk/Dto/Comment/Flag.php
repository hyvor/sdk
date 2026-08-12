<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Dto\Comment;

use Hyvor\Sdk\Product\Talk\Dto\User\CommentingUser;

final class Flag
{
    public function __construct(
        public readonly int $id,
        public readonly ?CommentingUser $user,
        public readonly int $comment_id,
        public readonly string $reason,
        public readonly bool $has_mod_seen,
    ) {
    }
}
