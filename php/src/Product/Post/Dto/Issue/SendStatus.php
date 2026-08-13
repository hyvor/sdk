<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Post\Dto\Issue;

enum SendStatus: string
{
    case PENDING = 'pending';
    case SENT = 'sent';
    case FAILED = 'failed';
}
