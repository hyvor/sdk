<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Comment;

/**
 * Input for `CommentsResource::list()` (`GET /comments`). All properties are
 * optional filters; null means "no filter" and is not sent (see
 * `Transport::normalize()`).
 */
final class ListCommentsRequest
{
    public function __construct(
        public readonly ?CommentListType $type = null,

        public readonly ?int $page_id = null,
        public readonly ?string $page_identifier = null,

        public readonly ?string $user_htid = null,
        public readonly ?string $user_sso_id = null,

        public readonly ?string $ip_address = null,

        public readonly ?int $badge_id = null,

        public readonly ?CommentListFilter $filter = null,

        public readonly ?string $search = null,

        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
        public readonly ?int $before_id = null,
    ) {
    }
}
