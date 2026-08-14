<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto;

final class Media
{
    public function __construct(
        public readonly int $id,
        public readonly int $created_at,
        public readonly MediaFolder $folder,
        public readonly string $url,
        public readonly int $size,
        public readonly string $extension,
    ) {
    }
}
