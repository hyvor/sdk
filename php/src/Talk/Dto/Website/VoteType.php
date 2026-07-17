<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Website;

enum VoteType: string
{
    case Both = 'both';
    case Upvotes = 'upvotes';
    case None = 'none';
}
