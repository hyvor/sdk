<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk;

use Hyvor\Sdk\Http\Transport;
use Hyvor\Sdk\Product\Talk\Org\OrgWebsitesResource;

/**
 * Namespace for the Hyvor Talk product, accessible via `$client->talk`.
 */
final class TalkClient
{
    /**
     * Org-level access to all websites, accessible via `$client->talk->websites`.
     */
    public readonly OrgWebsitesResource $websites;

    public function __construct(private readonly Transport $transport)
    {
        $this->websites = new OrgWebsitesResource($this->transport);
    }

    /**
     * Resource-level access to a single website.
     *
     * @param int|string $websiteId The website's ID.
     * @param string|null $apiKey A resource-level API key scoped to this
     *  website. If omitted, the client's org-level auth is used instead.
     * @param array<string, string> $headers Default headers merged into
     *  every request made through the returned client (and its
     *  sub-resources). Can be overridden per-call via
     *  `RequestOptions::$headers`.
     */
    public function website(int|string $websiteId, ?string $apiKey = null, array $headers = []): WebsiteClient
    {
        return new WebsiteClient($this->transport, $websiteId, $apiKey, $headers);
    }
}
