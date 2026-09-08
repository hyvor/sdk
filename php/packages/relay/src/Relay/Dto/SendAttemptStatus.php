<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Relay\Dto;

enum SendAttemptStatus: string
{
    case ACCEPTED = 'accepted';
    case DEFERRED = 'deferred';
    case BOUNCED = 'bounced';
    case PARTIAL = 'partial';
    case FAILED = 'failed';
}
