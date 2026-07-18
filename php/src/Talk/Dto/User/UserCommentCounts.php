<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\User;

final class UserCommentCounts
{
    public function __construct(
        public readonly int $total,
        public readonly int $published,
        public readonly int $pending,
        public readonly int $spam,
        public readonly int $deleted,
    ) {
    }
}
