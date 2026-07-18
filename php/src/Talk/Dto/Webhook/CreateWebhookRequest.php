<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Webhook;

final class CreateWebhookRequest
{
    /**
     * @param WebhookEvent[] $events
     */
    public function __construct(
        public readonly string $url,
        public readonly array $events,
    ) {
    }
}
