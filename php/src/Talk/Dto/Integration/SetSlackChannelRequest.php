<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Integration;

/**
 * Input for `SlackResource::setChannel()`. Either `channel` or
 * `channel_id` is required.
 */
final class SetSlackChannelRequest
{
    public function __construct(
        public readonly ?string $channel = null,
        public readonly ?string $channel_id = null,
    ) {
    }
}
