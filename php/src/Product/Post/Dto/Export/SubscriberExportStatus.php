<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Post\Dto\Export;

enum SubscriberExportStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
}
