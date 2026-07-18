<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Webhook;

final class WebhookConfiguration
{
    /**
     * @param WebhookEvent[] $events
     */
    public function __construct(
        public readonly int $id,
        public readonly string $url,
        public readonly array $events,
        public readonly string $secret,
    ) {
    }
}
