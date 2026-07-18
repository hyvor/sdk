<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\RequestOptions;
use Hyvor\Sdk\Talk\Dto\Domain\CreateDomainsRequest;
use Hyvor\Sdk\Talk\Dto\Domain\DeleteDomainRequest;
use Hyvor\Sdk\Talk\Dto\Domain\Domain;
use Hyvor\Sdk\Talk\Dto\Domain\UpdateDomainRequest;

/**
 * `$client->talk->website($websiteId)->domains`
 */
final class DomainsResource extends WebsiteScopedResource
{
    /**
     * GET /domains
     *
     * @return Domain[]
     * @throws HyvorApiException
     */
    public function list(?RequestOptions $options = null): array
    {
        $data = $this->request('GET', $this->path('/domains'), null, $options);

        return $this->transport->denormalizeList($data, Domain::class);
    }

    /**
     * POST /domains
     *
     * Creates one or more domains at once.
     *
     * @return Domain[]
     * @throws HyvorApiException
     */
    public function create(CreateDomainsRequest $request, ?RequestOptions $options = null): array
    {
        $body = $this->transport->normalize($request);
        $data = $this->request('POST', $this->path('/domains'), $body, $options);

        return $this->transport->denormalizeList($data, Domain::class);
    }

    /**
     * PATCH /domains
     *
     * @throws HyvorApiException
     */
    public function update(UpdateDomainRequest $request, ?RequestOptions $options = null): Domain
    {
        $body = $this->transport->normalize($request);
        $data = $this->request('PATCH', $this->path('/domains'), $body, $options);

        return $this->transport->denormalize($data, Domain::class);
    }

    /**
     * DELETE /domains
     *
     * @throws HyvorApiException
     */
    public function delete(DeleteDomainRequest $request, ?RequestOptions $options = null): void
    {
        $body = $this->transport->normalize($request);
        $this->request('DELETE', $this->path('/domains'), $body, $options);
    }
}
