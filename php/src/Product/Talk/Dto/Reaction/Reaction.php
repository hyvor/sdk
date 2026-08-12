<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Dto\Reaction;

use Hyvor\Sdk\Product\Talk\Dto\Page\Page;
use Hyvor\Sdk\Product\Talk\Dto\User\CommentingUser;

final class Reaction
{
    public function __construct(
        public readonly int $id,
        public readonly ?int $created_at,
        public readonly ?Page $page,
        public readonly ?CommentingUser $user,
        public readonly ReactionType $type,
    ) {
    }
}
