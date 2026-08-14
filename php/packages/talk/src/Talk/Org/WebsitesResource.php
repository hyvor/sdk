<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Org;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Http\Transport;
use Hyvor\Sdk\RequestOptions;

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
     *     owner_user_id?: int|null,
     * } $data
     *
     * @throws HyvorApiException
     */
    public function create(array $data, ?RequestOptions $options = null): void
    {
        $this->transport->request('POST', '/api/console/v1/websites', $data, $options);
    }
}
