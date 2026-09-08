<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Relay\Dto;

enum WebhookDeliveryStatus: string
{
    case PENDING = 'pending';
    case DELIVERED = 'delivered';
    case FAILED = 'failed';
}
