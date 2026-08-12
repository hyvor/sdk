<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Dto\Website;

enum ReactionDisplayType: string
{
    case TEXT = 'text';
    case IMAGE = 'image';
    case BOTH = 'both';
}
