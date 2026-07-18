<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\Issue;

enum SendStatus: string
{
    case PENDING = 'pending';
    case SENT = 'sent';
    case FAILED = 'failed';
}
