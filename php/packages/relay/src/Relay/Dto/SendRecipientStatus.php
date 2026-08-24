<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Relay\Dto;

enum SendRecipientStatus: string
{
    case QUEUED = 'queued';
    case ACCEPTED = 'accepted';
    case DEFERRED = 'deferred';
    case BOUNCED = 'bounced';
    case COMPLAINED = 'complained';
    case SUPPRESSED = 'suppressed';
    case FAILED = 'failed';
}
