<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Webhook;

final class ListWebhookDeliveriesRequest
{
    public function __construct(
        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
    ) {
    }
}
