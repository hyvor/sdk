<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Website;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Product\Talk\Dto\Domain\DomainObject;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->talk->website($websiteId)->domains`
 */
final class DomainsResource extends WebsiteScopedResource
{
    /**
     * POST /domains
     *
     * Adds to, or replaces, the website's domains, depending on `operation`.
     *
     * @param array{
     *     domains?: list<string>|array<string, string>,
     *     operation?: 'add'|'set',
     * } $data
     * @return DomainObject[]
     * @throws HyvorApiException
     */
    public function create(array $data, ?RequestOptions $options = null): array
    {
        $result = $this->request('POST', $this->path('/domains'), $data, $options);

        return $this->transport->denormalizeList($result, DomainObject::class);
    }
}
