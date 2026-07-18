<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Comment;

final class GetVotersRequest
{
    public function __construct(
        public readonly VoteDirection $type,
        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
    ) {
    }
}
