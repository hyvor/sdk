<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Relay\Dto;

final class Webhook
{
    public function __construct(
        public readonly int $id,
        public readonly string $url,
        public readonly ?string $description,
        /** @var string[] */
        public readonly array $events,
        public readonly ?string $secret,
    ) {
    }
}
