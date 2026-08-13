<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto;

enum SubscriberImportStatus: string
{
    case REQUIRES_INPUT = 'requires_input';
    case PENDING_APPROVAL = 'pending_approval';
    case IMPORTING = 'importing';
    case FAILED = 'failed';
    case COMPLETED = 'completed';
}
