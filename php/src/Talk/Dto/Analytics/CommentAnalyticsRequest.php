<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Analytics;

final class CommentAnalyticsRequest
{
    public function __construct(
        public readonly AnalyticsGroupBy $group_by,
        public readonly ?int $start_timestamp = null,
        public readonly ?int $end_timestamp = null,
    ) {
    }
}
