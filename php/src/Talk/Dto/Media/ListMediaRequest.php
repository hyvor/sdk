<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Media;

final class ListMediaRequest
{
    public function __construct(
        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
    ) {
    }
}
