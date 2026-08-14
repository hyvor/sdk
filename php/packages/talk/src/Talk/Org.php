<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk;

use Hyvor\Sdk\Http\Transport;
use Hyvor\Sdk\Talk\Org\WebsitesResource;

/**
 * Org-level access to resources, accessible via `$client->org`.
 *
 * Requires org-level auth (a cloud API key or token provider), since it
 * is not scoped to a single resource.
 */
final class Org
{
    public readonly WebsitesResource $websites;

    public function __construct(Transport $transport)
    {
        $this->websites = new WebsitesResource($transport);
    }
}
