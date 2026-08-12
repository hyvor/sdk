<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Dto\User;

final class UserCounts
{
    public function __construct(
        public readonly UserCommentCounts $comments,
        public readonly UserFlagCounts $flags,
    ) {
    }
}
