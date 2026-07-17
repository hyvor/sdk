<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto;

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

    /**
     * @return array<string, string>
     * @internal
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'domain' => $this->domain,
        ];
    }
}
