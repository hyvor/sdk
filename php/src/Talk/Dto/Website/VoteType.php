<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Website;

enum VoteType: string
{
    case BOTH = 'both';
    case UPVOTES = 'upvotes';
    case NONE = 'none';
}
