<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Http\Transport;
use Hyvor\Sdk\Product\Talk\Website\DomainsResource;
use Hyvor\Sdk\Product\Talk\Website\ModeratorsResource;
use Hyvor\Sdk\RequestOptions;

/**
 * Resource-level access to a single website, accessible via
 * `$client->talk->website($websiteId)`.
 *
 * Authenticated either with the client's org-level auth (a cloud API key or
 * token provider, which must have access to this website), or with a
 * resource-level API key scoped to this website, passed as `$apiKey`.
 */
final class WebsiteClient
{
    public readonly DomainsResource $domains;
    public readonly ModeratorsResource $moderators;

    /**
     * @param array<string, string> $headers Default headers merged into
     *  every request made through this client and its sub-resources.
     */
    public function __construct(
        private readonly Transport $transport,
        private readonly int|string $websiteId,
        private readonly ?string $apiKey = null,
        private readonly array $headers = [],
    ) {
        $this->domains = new DomainsResource($transport, $websiteId, $apiKey, $headers);
        $this->moderators = new ModeratorsResource($transport, $websiteId, $apiKey, $headers);
    }

    private function path(string $suffix = ''): string
    {
        return "/api/console/v1/{$this->websiteId}{$suffix}";
    }

    /**
     * DELETE /website
     *
     * @throws HyvorApiException
     */
    public function delete(?RequestOptions $options = null): void
    {
        $this->transport->request(
            'DELETE',
            $this->path('/website'),
            null,
            $options,
            $this->apiKey,
            $this->headers,
        );
    }
}
