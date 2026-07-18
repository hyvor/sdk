<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Webhook;

final class WebhookDelivery
{
    public function __construct(
        public readonly int $id,
        public readonly ?int $created_at,
        public readonly int $webhook_configuration_id,
        public readonly string $url,
        public readonly WebhookEvent $event,
        public readonly WebhookDeliveryStatus $status,
        public readonly ?string $request_body,
        public readonly ?int $num_attempts,
        public readonly ?int $last_attempt_at,
        public readonly ?string $response_body,
        public readonly ?int $response_code,
    ) {
    }
}
