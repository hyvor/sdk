<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Dto\Website;

enum Sort: string
{
    case TOP = 'top';
    case NEWEST = 'newest';
    case OLDEST = 'oldest';
}
