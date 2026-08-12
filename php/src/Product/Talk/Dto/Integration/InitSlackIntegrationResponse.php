<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Dto\Integration;

final class InitSlackIntegrationResponse
{
    public function __construct(
        public readonly string $url,
    ) {
    }
}
