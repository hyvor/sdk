<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Dto\Rating;

use Hyvor\Sdk\Product\Talk\Dto\Page\Page;
use Hyvor\Sdk\Product\Talk\Dto\User\CommentingUser;

final class Rating
{
    public function __construct(
        public readonly int $id,
        public readonly ?int $created_at,
        public readonly ?Page $page,
        public readonly ?CommentingUser $user,
        public readonly int $rating,
    ) {
    }
}
