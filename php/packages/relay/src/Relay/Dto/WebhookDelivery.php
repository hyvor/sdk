<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Relay\Dto;

final class WebhookDelivery
{
    public function __construct(
        public readonly int $id,
        public readonly int $created_at,
        public readonly string $url,
        public readonly WebhooksEventEnum $event,
        public readonly WebhookDeliveryStatus $status,
        public readonly ?string $response,
        public readonly ?int $response_code,
        public readonly int $try_count,
        public readonly string $request_body,
    ) {
    }
}
