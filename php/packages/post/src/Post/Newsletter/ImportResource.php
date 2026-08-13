<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Newsletter;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Post\Dto\SubscriberImport;
use Hyvor\Sdk\Post\Newsletter;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->newsletter($newsletterId)->import`
 */
final class ImportResource
{
    public function __construct(private readonly Newsletter $client)
    {
    }

    /**
     * POST /api/console/imports/upload
     *
     * @param array{
     *     source: string,
     * } $data
     *
     * @throws HyvorApiException
     */
    public function upload(array $data, ?RequestOptions $options = null): SubscriberImport
    {
        $result = $this->client->request('POST', $this->client->path('/api/console/imports/upload'), $data, $options);

        return $this->client->transport->denormalize($result, SubscriberImport::class);
    }

    /**
     * POST /api/console/imports/{id}
     *
     * @param array{
     *     mapping: array<string, string|null>,
     * } $data
     *
     * @throws HyvorApiException
     */
    public function start(int $id, array $data, ?RequestOptions $options = null): SubscriberImport
    {
        $result = $this->client->request('POST', $this->client->path('/api/console/imports/' . $id), $data, $options);

        return $this->client->transport->denormalize($result, SubscriberImport::class);
    }

    /**
     * GET /api/console/imports
     *
     * @return SubscriberImport[]
     *
     * @throws HyvorApiException
     */
    public function list(?RequestOptions $options = null): array
    {
        $result = $this->client->request('GET', $this->client->path('/api/console/imports'), null, $options);

        return $this->client->transport->denormalizeList($result, SubscriberImport::class);
    }

    /**
     * GET /api/console/imports/limits
     *
     * @return array<mixed>
     *
     * @throws HyvorApiException
     */
    public function getlimits(?RequestOptions $options = null): array
    {
        return $this->client->request('GET', $this->client->path('/api/console/imports/limits'), null, $options);
    }
}
