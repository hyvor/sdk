<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Analytics;

final class CommentAnalyticsPoint
{
    public function __construct(
        public readonly string $date,
        public readonly int $count,
        public readonly int $published,
        public readonly int $pending,
        public readonly int $spam,
        public readonly int $deleted,
    ) {
    }
}
