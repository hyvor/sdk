<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Comment;

/**
 * Input for `CommentsResource::markRead()` (`POST /comments/read`). If
 * `status` is null, comments of all statuses are marked as read.
 */
final class MarkCommentsReadRequest
{
    public function __construct(
        public readonly ?CommentStatus $status = null,
    ) {
    }
}
