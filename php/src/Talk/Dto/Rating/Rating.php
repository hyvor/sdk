<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Rating;

use Hyvor\Sdk\Talk\Dto\Page\Page;
use Hyvor\Sdk\Talk\Dto\User\CommentingUser;

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
