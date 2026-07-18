<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Comment;

final class UnreadCommentCounts
{
    public function __construct(
        public readonly int $published,
        public readonly int $pending,
        public readonly int $spam,
        public readonly int $deleted,
    ) {
    }
}
