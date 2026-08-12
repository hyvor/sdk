<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Dto\Comment;

final class ReplyToCommentRequest
{
    public function __construct(
        public readonly string $body,
    ) {
    }
}
