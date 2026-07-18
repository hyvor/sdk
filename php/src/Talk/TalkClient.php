<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk;

use Hyvor\Sdk\Http\Transport;
use Hyvor\Sdk\Talk\Resources\WebsitesResource;

/**
 * Namespace for the Hyvor Talk product, accessible via `$client->talk`.
 */
final class TalkClient
{
    /**
     * Org-level access to all websites (e.g. `->create()`).
     */
    public readonly WebsitesResource $websites;

    public function __construct(private readonly Transport $transport)
    {
        $this->websites = new WebsitesResource($transport);
    }

    /**
     * Resource-level access to a single website.
     *
     * @param int|string $websiteId The website's ID.
     * @param string|null $apiKey A resource-level API key scoped to this
     *  website. If omitted, the client's org-level auth is used instead.
     */
    public function website(int|string $websiteId, ?string $apiKey = null): WebsiteClient
    {
        return new WebsiteClient($this->transport, $websiteId, $apiKey);
    }
}
