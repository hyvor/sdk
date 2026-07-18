<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Analytics;

final class CreditAnalyticsPoint
{
    public function __construct(
        public readonly string $date,
        public readonly int $credits,
        public readonly int $events,
    ) {
    }
}
