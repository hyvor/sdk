<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Comment;

use Hyvor\Sdk\Talk\Dto\Moderator\Mod;
use Hyvor\Sdk\Talk\Dto\Rule\Rule;

final class CommentHistory
{
    public function __construct(
        public readonly int $id,
        public readonly int $created_at,

        public readonly CommentHistoryType $type,
        public readonly ?CommentStatus $new_status,

        public readonly ?Rule $rule,
        public readonly ?Mod $mod,

        public readonly ?CommentHistoryVia $via,
        public readonly ?string $additional_info,
    ) {
    }
}
