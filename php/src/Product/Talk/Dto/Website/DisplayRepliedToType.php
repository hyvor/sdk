<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Dto\Website;

enum DisplayRepliedToType: string
{
    case NONE = 'none';
    case DEEP = 'deep';
    case ALL = 'all';
}
