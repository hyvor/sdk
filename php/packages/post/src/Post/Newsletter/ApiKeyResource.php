<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Newsletter;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Post\Dto\ApiKey;
use Hyvor\Sdk\Post\Newsletter;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->newsletter($newsletterId)->api_key`
 */
final class ApiKeyResource
{
    public function __construct(private readonly Newsletter $client)
    {
    }

    /**
     * GET /api/console/api-keys
     *
     * @return ApiKey[]
     *
     * @throws HyvorApiException
     */
    public function list(?RequestOptions $options = null): array
    {
        $result = $this->client->request('GET', $this->client->path('/api/console/api-keys'), null, $options);

        return $this->client->transport->denormalizeList($result, ApiKey::class);
    }

    /**
     * POST /api/console/api-keys
     *
     * @param array{
     *     name: string,
     *     scopes: list<string>,
     * } $data
     *
     * @throws HyvorApiException
     */
    public function create(array $data, ?RequestOptions $options = null): ApiKey
    {
        $result = $this->client->request('POST', $this->client->path('/api/console/api-keys'), $data, $options);

        return $this->client->transport->denormalize($result, ApiKey::class);
    }

    /**
     * POST /api/console/api-keys/{id}
     *
     * @throws HyvorApiException
     */
    public function regenerate(int $id, ?RequestOptions $options = null): ApiKey
    {
        $result = $this->client->request('POST', $this->client->path('/api/console/api-keys/' . $id), null, $options);

        return $this->client->transport->denormalize($result, ApiKey::class);
    }

    /**
     * DELETE /api/console/api-keys/{id}
     *
     * @throws HyvorApiException
     */
    public function delete(int $id, ?RequestOptions $options = null): void
    {
        $this->client->request('DELETE', $this->client->path('/api/console/api-keys/' . $id), null, $options);
    }

    /**
     * PATCH /api/console/api-keys/{id}
     *
     * @param array{
     *     name: string,
     *     is_enabled: bool,
     *     scopes: list<string>|array<string, string>,
     * } $data
     *
     * @throws HyvorApiException
     */
    public function update(int $id, array $data, ?RequestOptions $options = null): ApiKey
    {
        $result = $this->client->request('PATCH', $this->client->path('/api/console/api-keys/' . $id), $data, $options);

        return $this->client->transport->denormalize($result, ApiKey::class);
    }
}
