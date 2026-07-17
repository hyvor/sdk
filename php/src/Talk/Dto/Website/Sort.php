<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Website;

enum Sort: string
{
    case Top = 'top';
    case Newest = 'newest';
    case Oldest = 'oldest';
}
