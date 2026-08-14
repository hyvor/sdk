<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Newsletter;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Post\Dto\SubscriberMetadataDefinition;
use Hyvor\Sdk\Post\Newsletter;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->newsletter($newsletterId)->subscriber_metadata`
 */
final class SubscriberMetadataResource
{
    public function __construct(private readonly Newsletter $client)
    {
    }

    /**
     * POST /api/console/subscriber-metadata-definitions
     *
     * @param array{
     *     key: string,
     *     name: string,
     * } $data
     *
     * @throws HyvorApiException
     */
    public function create(array $data, ?RequestOptions $options = null): SubscriberMetadataDefinition
    {
        $result = $this->client->request('POST', $this->client->path('/api/console/subscriber-metadata-definitions'), $data, $options);

        return $this->client->transport->denormalize($result, SubscriberMetadataDefinition::class);
    }

    /**
     * DELETE /api/console/subscriber-metadata-definitions/{id}
     *
     * @throws HyvorApiException
     */
    public function delete(int $id, ?RequestOptions $options = null): void
    {
        $this->client->request('DELETE', $this->client->path('/api/console/subscriber-metadata-definitions/' . $id), null, $options);
    }

    /**
     * PATCH /api/console/subscriber-metadata-definitions/{id}
     *
     * @param array{
     *     name: string,
     * } $data
     *
     * @throws HyvorApiException
     */
    public function update(int $id, array $data, ?RequestOptions $options = null): SubscriberMetadataDefinition
    {
        $result = $this->client->request('PATCH', $this->client->path('/api/console/subscriber-metadata-definitions/' . $id), $data, $options);

        return $this->client->transport->denormalize($result, SubscriberMetadataDefinition::class);
    }
}
