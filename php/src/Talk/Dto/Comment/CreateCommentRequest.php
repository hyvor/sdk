<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Comment;

/**
 * Input for `CommentsResource::create()` (`POST /comment`). Either
 * `page_id` or `page_identifier` is required; either `body` or `body_html`
 * is required; either `user_sso_id` or `guest_name` is required.
 */
final class CreateCommentRequest
{
    public function __construct(
        public readonly ?int $page_id = null,
        public readonly ?string $page_identifier = null,
        public readonly ?string $body = null,
        public readonly ?string $body_html = null,
        public readonly ?string $user_sso_id = null,
        public readonly ?string $guest_name = null,
        public readonly ?string $guest_email = null,
        public readonly ?int $parent_id = null,
        public readonly ?int $created_at = null,
        public readonly ?string $ip = null,

        public readonly bool $check_premoderation = true,
        public readonly bool $spam_detection = true,
        public readonly bool $rules = true,
    ) {
    }
}
