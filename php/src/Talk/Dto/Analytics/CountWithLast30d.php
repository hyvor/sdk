<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Analytics;

final class CountWithLast30d
{
    public function __construct(
        public readonly int $total,
        public readonly int $last_30d,
    ) {
    }
}
