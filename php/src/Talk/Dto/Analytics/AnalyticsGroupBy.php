<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Analytics;

enum AnalyticsGroupBy: string
{
    case DAY = 'day';
    case WEEK = 'week';
    case MONTH = 'month';
    case YEAR = 'year';
}
