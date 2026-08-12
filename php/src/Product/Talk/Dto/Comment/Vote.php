<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Dto\Comment;

use Hyvor\Sdk\Product\Talk\Dto\User\CommentingUser;

final class Vote
{
    public function __construct(
        public readonly int $id,
        public readonly ?int $created_at,
        public readonly int $comment_id,
        public readonly ?CommentingUser $user,
        public readonly ?string $ip_hash,
        public readonly VoteDirection $type,
    ) {
    }
}
