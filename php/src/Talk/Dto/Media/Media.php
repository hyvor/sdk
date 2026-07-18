<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Media;

final class Media
{
    public function __construct(
        public readonly int $id,
        public readonly int $created_at,
        public readonly ?int $comment_id,
        public readonly string $name,
        public readonly int $size,
        public readonly string $url,
    ) {
    }
}
