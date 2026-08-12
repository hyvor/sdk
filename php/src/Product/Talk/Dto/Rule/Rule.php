<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Dto\Rule;

use Hyvor\Sdk\Product\Talk\Dto\Comment\CommentStatus;

final class Rule
{
    public function __construct(
        public readonly int $id,
        public readonly RuleType $type,
        public readonly string $value,
        public readonly CommentStatus $to_status,
        public readonly int $priority,
    ) {
    }
}
