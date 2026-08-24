<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Relay;

use Hyvor\Sdk\Http\Transport;
use Hyvor\Sdk\Relay\Org\ProjectsResource;

/**
 * Org-level access to resources, accessible via `$client->org`.
 *
 * Requires org-level auth (a cloud API key or token provider), since it
 * is not scoped to a single resource.
 */
final class Org
{
    public readonly ProjectsResource $projects;

    public function __construct(Transport $transport)
    {
        $this->projects = new ProjectsResource($transport);
    }
}
