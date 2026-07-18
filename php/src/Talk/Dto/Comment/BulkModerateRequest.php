<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Comment;

/**
 * Input for `CommentsResource::bulkModerate()` (`POST /comments/bulk-moderate`).
 * Only one of `user_htid`/`user_sso_id`, `ip_address`, or `comment_ids`
 * should be set.
 */
final class BulkModerateRequest
{
    /**
     * @param int[]|null $comment_ids
     */
    public function __construct(
        public readonly CommentStatus $status,
        public readonly ?string $user_htid = null,
        public readonly ?string $user_sso_id = null,
        public readonly ?string $ip_address = null,
        public readonly ?array $comment_ids = null,
    ) {
    }
}
