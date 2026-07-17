<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Http\Transport;
use Hyvor\Sdk\RequestOptions;
use Hyvor\Sdk\Talk\Dto\CreateWebsiteRequest;
use Hyvor\Sdk\Talk\Dto\Website;

/**
 * `website` is the main resource of the Talk product.
 */
final class WebsiteResource
{
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * GET /website
     *
     * @throws HyvorApiException
     */
    public function get(?RequestOptions $options = null): Website
    {
        $data = $this->transport->request('GET', '/api/talk/website', null, $options);

        return Website::fromArray($data);
    }

    /**
     * POST /website
     *
     * @throws HyvorApiException
     */
    public function create(CreateWebsiteRequest $request, ?RequestOptions $options = null): Website
    {
        $data = $this->transport->request('POST', '/api/talk/website', $request->toArray(), $options);

        return Website::fromArray($data);
    }
}
