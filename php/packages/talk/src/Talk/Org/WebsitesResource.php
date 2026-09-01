<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Org;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Http\Transport;
use Hyvor\Sdk\RequestOptions;
use Hyvor\Sdk\Talk\Dto\WebsitesCreateResponse;

/**
 * `$client->org->websites`
 */
final class WebsitesResource
{
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * POST /api/console/v1/websites
     *
     * @param array{
     *     name: string,
     *     domain: string,
     *     metadata?: array<string, mixed>,
     *     start_trial?: bool,
     * } $data
     *
     * @throws HyvorApiException
     */
    public function create(array $data, ?RequestOptions $options = null): WebsitesCreateResponse
    {
        $result = $this->transport->request('POST', '/api/console/v1/websites', $data, $options);

        return $this->transport->denormalize($result, WebsitesCreateResponse::class);
    }
}
