<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post;

use Hyvor\Sdk\Http\Transport;
use Hyvor\Sdk\Post\Org\DomainResource;
use Hyvor\Sdk\Post\Org\NewslettersResource;

/**
 * Org-level access to resources, accessible via `$client->org`.
 *
 * Requires org-level auth (a cloud API key or token provider), since it
 * is not scoped to a single resource.
 */
final class Org
{
    public readonly DomainResource $domain;
    public readonly NewslettersResource $newsletters;

    public function __construct(Transport $transport)
    {
        $this->domain = new DomainResource($transport);
        $this->newsletters = new NewslettersResource($transport);
    }
}
