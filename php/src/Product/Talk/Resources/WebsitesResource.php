<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Http\Transport;
use Hyvor\Sdk\Product\Talk\Dto\Website\CreateWebsiteRequest;
use Hyvor\Sdk\Product\Talk\Dto\Website\Website;
use Hyvor\Sdk\RequestOptions;

/**
 * Org-level access to websites, accessible via `$client->talk->websites`.
 *
 * Requires org-level auth (a cloud API key or token provider), since it is
 * not scoped to a single website.
 */
final class WebsitesResource
{
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * POST /api/console/v1/websites
     *
     * @throws HyvorApiException
     */
    public function create(CreateWebsiteRequest $request, ?RequestOptions $options = null): Website
    {
        $body = $this->transport->normalize($request);
        $data = $this->transport->request('POST', '/api/console/v1/websites', $body, $options);

        return $this->transport->denormalize($data, Website::class);
    }
}
