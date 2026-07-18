<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Analytics;

final class WebsiteStats
{
    public function __construct(
        public readonly CountWithLast30d $comments,
        public readonly CountWithLast30d $users,
        public readonly CountWithLast30d $members,
        public readonly CountWithLast30d $newsletter_subscribers,
    ) {
    }
}
