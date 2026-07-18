<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Page;

final class Page
{
    public function __construct(
        public readonly int $id,
        public readonly int $created_at,
        public readonly ?int $last_commented_at,
        public readonly string $title,
        public readonly string $identifier,
        public readonly string $url,
        public readonly bool $is_closed,
        public readonly bool $is_premoderation_on,
        public readonly ?string $author_email,
        public readonly int $comments_count,
        public readonly PageRatingsSummary $ratings,
        public readonly PageReactionsSummary $reactions,
    ) {
    }
}
