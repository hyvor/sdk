<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Website;

/**
 * Input for WebsiteResource::create().
 */
final class CreateWebsiteRequest
{
    public function __construct(
        public readonly string $name,
        public readonly string $domain,
    ) {
    }
}
