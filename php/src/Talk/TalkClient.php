<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk;

use Hyvor\Sdk\Http\Transport;
use Hyvor\Sdk\Talk\Resources\WebsiteResource;

/**
 * Namespace for the Hyvor Talk product, accessible via `$client->talk`.
 */
final class TalkClient
{
    public readonly WebsiteResource $website;

    public function __construct(Transport $transport)
    {
        $this->website = new WebsiteResource($transport);
    }
}
