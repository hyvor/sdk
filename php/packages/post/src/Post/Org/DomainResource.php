<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Org;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Http\Transport;
use Hyvor\Sdk\Post\Dto\Domain;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->org->domain`
 */
final class DomainResource
{
    public function __construct(private readonly Transport $transport)
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
        $result = $this->transport->request('GET', '/api/console/domains', null, $options);

        return $this->transport->denormalizeList($result, Domain::class);
    }

    /**
     * POST /api/console/domains
     *
     * @param array{
     *     domain: string,
     * } $data
     *
     * @throws HyvorApiException
     */
    public function create(array $data, ?RequestOptions $options = null): Domain
    {
        $result = $this->transport->request('POST', '/api/console/domains', $data, $options);

        return $this->transport->denormalize($result, Domain::class);
    }

    /**
     * POST /api/console/domains/{id}/verify
     *
     * @return array<mixed>
     *
     * @throws HyvorApiException
     */
    public function verify(int $id, ?RequestOptions $options = null): array
    {
        return $this->transport->request('POST', '/api/console/domains/' . $id . '/verify', null, $options);
    }

    /**
     * DELETE /api/console/domains/{id}
     *
     * @throws HyvorApiException
     */
    public function delete(int $id, ?RequestOptions $options = null): void
    {
        $this->transport->request('DELETE', '/api/console/domains/' . $id, null, $options);
    }
}
