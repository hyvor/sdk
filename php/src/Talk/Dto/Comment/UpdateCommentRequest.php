<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Comment;

/**
 * Input for `CommentsResource::update()` (`PATCH /comment/{id}`). Either
 * `body` or `body_html` is required when editing the comment's content.
 */
final class UpdateCommentRequest
{
    public function __construct(
        public readonly ?CommentStatus $status = null,
        public readonly ?bool $is_featured = null,
        public readonly ?bool $is_loved = null,
        public readonly ?bool $has_mod_seen = null,
        public readonly ?string $body = null,
        public readonly ?string $body_html = null,
        public readonly ?int $created_at = null,
        public readonly ?string $guest_name = null,
        public readonly ?string $guest_email = null,

        // only considered if body or body_html is set
        public readonly bool $is_author = false,
        public readonly bool $check_premoderation = true,
        public readonly bool $spam_detection = true,
        public readonly bool $rules = true,
    ) {
    }
}
