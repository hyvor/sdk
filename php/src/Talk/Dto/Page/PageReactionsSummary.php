<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Page;

final class PageReactionsSummary
{
    public function __construct(
        public readonly int $superb,
        public readonly int $love,
        public readonly int $wow,
        public readonly int $sad,
        public readonly int $laugh,
        public readonly int $angry,
    ) {
    }
}
