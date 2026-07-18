<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\Subscriber;

enum BulkSubscriberAction: string
{
    case DELETE = 'delete';
    case STATUS_CHANGE = 'status_change';
    case METADATA_UPDATE = 'metadata_update';
}
