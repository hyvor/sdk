<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Comment;

final class ReplyToCommentRequest
{
    public function __construct(
        public readonly string $body,
    ) {
    }
}
