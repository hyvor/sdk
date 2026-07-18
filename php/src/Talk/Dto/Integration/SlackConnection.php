<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Integration;

final class SlackConnection
{
    public function __construct(
        public readonly bool $has_token,
        public readonly ?string $channel_name,
        public readonly ?string $channel_id,
    ) {
    }
}
