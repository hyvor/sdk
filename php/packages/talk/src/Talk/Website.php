<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Http\Transport;
use Hyvor\Sdk\RequestOptions;
use Hyvor\Sdk\Talk\Website\DomainsResource;
use Hyvor\Sdk\Talk\Website\ModsResource;

/**
 * Resource-level access to a single website, accessible via
 * `$client->website($websiteId)`.
 *
 * Authenticated either with the client's org-level auth (a cloud API
 * key or token provider, which must have access to this website), or
 * with a resource-level API key, passed as `$apiKey`.
 */
final class Website
{
    public readonly DomainsResource $domains;
    public readonly ModsResource $mods;

    /**
     * @param array<string, string> $headers Default headers merged into
     *  every request made through this client and its sub-resources.
     */
    public function __construct(
        public readonly Transport $transport,
        private readonly int|string $websiteId,
        private readonly ?string $apiKey = null,
        private readonly array $headers = [],
    ) {
        $this->domains = new DomainsResource($this);
        $this->mods = new ModsResource($this);
    }

    public function path(string $suffix = ''): string
    {
        return "/api/console/v1/{$this->websiteId}{$suffix}";
    }

    /**
     * @param array<mixed>|null $jsonBody
     * @return array<mixed>
     *
     * @throws HyvorApiException
     */
    public function request(
        string $method,
        string $path,
        ?array $jsonBody = null,
        ?RequestOptions $options = null,
    ): array {
        return $this->transport->request($method, $path, $jsonBody, $options, $this->apiKey, $this->headers);
    }

    /**
     * DELETE /website
     *
     * @throws HyvorApiException
     */
    public function delete(?RequestOptions $options = null): void
    {
        $this->request('DELETE', $this->path('/website'), null, $options);
    }
}
