<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Newsletter;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Post\Dto\SubscriberExport;
use Hyvor\Sdk\Post\Newsletter;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->newsletter($newsletterId)->export`
 */
final class ExportResource
{
    public function __construct(private readonly Newsletter $client)
    {
    }

    /**
     * GET /api/console/export
     *
     * @return SubscriberExport[]
     *
     * @throws HyvorApiException
     */
    public function list(?RequestOptions $options = null): array
    {
        $result = $this->client->request('GET', $this->client->path('/api/console/export'), null, $options);

        return $this->client->transport->denormalizeList($result, SubscriberExport::class);
    }

    /**
     * POST /api/console/export
     *
     * @throws HyvorApiException
     */
    public function create(?RequestOptions $options = null): SubscriberExport
    {
        $result = $this->client->request('POST', $this->client->path('/api/console/export'), null, $options);

        return $this->client->transport->denormalize($result, SubscriberExport::class);
    }
}
