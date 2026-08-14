<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Newsletter;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Post\Dto\ListObject;
use Hyvor\Sdk\Post\Newsletter;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->newsletter($newsletterId)->list`
 */
final class ListResource
{
    public function __construct(private readonly Newsletter $client)
    {
    }

    /**
     * POST /api/console/lists
     *
     * @param array{
     *     name: string,
     *     description?: string|null,
     * } $data
     *
     * @throws HyvorApiException
     */
    public function create(array $data, ?RequestOptions $options = null): ListObject
    {
        $result = $this->client->request('POST', $this->client->path('/api/console/lists'), $data, $options);

        return $this->client->transport->denormalize($result, ListObject::class);
    }

    /**
     * DELETE /api/console/lists/{id}
     *
     * @throws HyvorApiException
     */
    public function delete(int $id, ?RequestOptions $options = null): void
    {
        $this->client->request('DELETE', $this->client->path('/api/console/lists/' . $id), null, $options);
    }

    /**
     * PATCH /api/console/lists/{id}
     *
     * @param array{
     *     name?: string|null,
     *     description?: string|null,
     * } $data
     *
     * @throws HyvorApiException
     */
    public function update(int $id, array $data, ?RequestOptions $options = null): ListObject
    {
        $result = $this->client->request('PATCH', $this->client->path('/api/console/lists/' . $id), $data, $options);

        return $this->client->transport->denormalize($result, ListObject::class);
    }
}
