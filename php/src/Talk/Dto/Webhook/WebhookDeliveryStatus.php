<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Webhook;

enum WebhookDeliveryStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case RETRYING = 'retrying';
    case FAILED = 'failed';
}
