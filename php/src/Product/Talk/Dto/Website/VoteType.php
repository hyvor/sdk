<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Dto\Website;

enum VoteType: string
{
    case BOTH = 'both';
    case UPVOTES = 'upvotes';
    case NONE = 'none';
}
