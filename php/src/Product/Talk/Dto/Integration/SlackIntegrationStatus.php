<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Dto\Integration;

final class SlackIntegrationStatus
{
    public function __construct(
        public readonly SlackConnection $connection,
    ) {
    }
}
