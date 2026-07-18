<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Webhook;

final class UpdateWebhookRequest
{
    /**
     * @param WebhookEvent[]|null $events
     */
    public function __construct(
        public readonly ?string $url = null,
        public readonly ?array $events = null,
    ) {
    }
}
