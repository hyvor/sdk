<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Website;

use Hyvor\Sdk\Http\Transport;
use Hyvor\Sdk\RequestOptions;

/**
 * Base for resources scoped to a single website (Domains, Moderators, ...),
 * accessible via `$client->talk->website($websiteId)->{resource}`.
 *
 * @internal
 */
abstract class WebsiteScopedResource
{
    /**
     * @param array<string, string> $headers Default headers from
     *  `TalkClient::website()`, merged into every request made by this
     *  resource.
     */
    public function __construct(
        protected readonly Transport $transport,
        protected readonly int|string $websiteId,
        protected readonly ?string $apiKey = null,
        protected readonly array $headers = [],
    ) {
    }

    /**
     * Builds a Console API path: `/api/console/v1/{websiteId}{suffix}`.
     */
    protected function path(string $suffix = ''): string
    {
        return "/api/console/v1/{$this->websiteId}{$suffix}";
    }

    /**
     * @param array<mixed>|null $jsonBody
     * @param array<string, string> $extraHeaders Endpoint-specific headers,
     *  merged under the resource's default headers.
     * @return array<mixed>
     */
    protected function request(
        string $method,
        string $path,
        ?array $jsonBody = null,
        ?RequestOptions $options = null,
        array $extraHeaders = [],
    ): array {
        return $this->transport->request(
            $method,
            $path,
            $jsonBody,
            $options,
            $this->apiKey,
            [...$this->headers, ...$extraHeaders],
        );
    }
}
