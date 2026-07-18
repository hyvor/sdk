<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Http\Transport;
use Hyvor\Sdk\RequestOptions;
use Hyvor\Sdk\Talk\Dto\Website\Website;

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
    public function __construct(
        private readonly Transport $transport,
        private readonly int|string $websiteId,
        private readonly ?string $apiKey = null,
    ) {
    }

    /**
     * GET /api/talk/websites/{websiteId}
     *
     * @throws HyvorApiException
     */
    public function get(?RequestOptions $options = null): Website
    {
        $data = $this->transport->request(
            'GET',
            "/api/talk/websites/{$this->websiteId}",
            null,
            $options,
            $this->apiKey,
        );

        return $this->transport->denormalize($data, Website::class);
    }
}
