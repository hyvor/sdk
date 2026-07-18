<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Page;

final class ListPagesRequest
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?PageFilter $filter = null,
        public readonly ?PageSort $sort = null,
        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
    ) {
    }
}
