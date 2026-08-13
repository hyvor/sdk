<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Website;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\RequestOptions;
use Hyvor\Sdk\Talk\Dto\Domain;
use Hyvor\Sdk\Talk\Website;

/**
 * `$client->website($websiteId)->domains`
 */
final class DomainsResource
{
    public function __construct(private readonly Website $client)
    {
    }

    /**
     * POST /domains
     *
     * @param array{
     *     domains?: list<string>,
     *     operation?: 'add'|'set',
     * } $data
     *
     * @return Domain[]
     *
     * @throws HyvorApiException
     */
    public function update(array $data, ?RequestOptions $options = null): array
    {
        $result = $this->client->request('POST', $this->client->path('/domains'), $data, $options);

        return $this->client->transport->denormalizeList($result, Domain::class);
    }
}
