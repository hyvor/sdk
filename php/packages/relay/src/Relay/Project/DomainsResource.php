<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Relay\Project;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Relay\Dto\Domain;
use Hyvor\Sdk\Relay\Project;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->project($projectId)->domains`
 */
final class DomainsResource
{
    public function __construct(private readonly Project $client)
    {
    }

    /**
     * GET /api/console/domains
     *
     * @return Domain[]
     *
     * @throws HyvorApiException
     */
    public function list(?RequestOptions $options = null): array
    {
        $result = $this->client->request('GET', $this->client->path('/api/console/domains'), null, $options);

        return $this->client->transport->denormalizeList($result, Domain::class);
    }

    /**
     * POST /api/console/domains
     *
     * @param array{
     *     domain: string,
     *     dkim_selector?: string|null,
     *     dkim_private_key?: string|null,
     * } $data
     *
     * @throws HyvorApiException
     */
    public function create(array $data, ?RequestOptions $options = null): Domain
    {
        $result = $this->client->request('POST', $this->client->path('/api/console/domains'), $data, $options);

        return $this->client->transport->denormalize($result, Domain::class);
    }

    /**
     * DELETE /api/console/domains
     *
     * @param array{
     *     id?: int|null,
     *     domain?: string|null,
     * } $data
     *
     * @throws HyvorApiException
     */
    public function delete(array $data, ?RequestOptions $options = null): void
    {
        $this->client->request('DELETE', $this->client->path('/api/console/domains'), $data, $options);
    }

    /**
     * POST /api/console/domains/verify
     *
     * @param array{
     *     id?: int|null,
     *     domain?: string|null,
     * } $data
     *
     * @throws HyvorApiException
     */
    public function verify(array $data, ?RequestOptions $options = null): Domain
    {
        $result = $this->client->request('POST', $this->client->path('/api/console/domains/verify'), $data, $options);

        return $this->client->transport->denormalize($result, Domain::class);
    }

    /**
     * GET /api/console/domains/by
     *
     * @param array{
     *     id?: int|null,
     *     domain?: string|null,
     * } $data
     *
     * @throws HyvorApiException
     */
    public function get(array $data, ?RequestOptions $options = null): Domain
    {
        $result = $this->client->request('GET', $this->client->path('/api/console/domains/by'), $data, $options);

        return $this->client->transport->denormalize($result, Domain::class);
    }
}
