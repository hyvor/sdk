<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Rating;

final class ListRatingsRequest
{
    public function __construct(
        public readonly ?int $page_id = null,
        public readonly ?string $user_htid = null,
        public readonly ?string $user_sso_id = null,
        public readonly ?int $rating = null,
        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
    ) {
    }
}
