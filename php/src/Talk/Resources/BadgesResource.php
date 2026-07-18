<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\RequestOptions;
use Hyvor\Sdk\Talk\Dto\Badge\Badge;
use Hyvor\Sdk\Talk\Dto\Badge\CreateBadgeRequest;
use Hyvor\Sdk\Talk\Dto\Badge\UpdateBadgeRequest;

/**
 * `$client->talk->website($websiteId)->badges`
 */
final class BadgesResource extends WebsiteScopedResource
{
    /**
     * GET /badges
     *
     * @return Badge[]
     * @throws HyvorApiException
     */
    public function list(?RequestOptions $options = null): array
    {
        $data = $this->request('GET', $this->path('/badges'), null, $options);

        return $this->transport->denormalizeList($data, Badge::class);
    }

    /**
     * POST /badge
     *
     * @throws HyvorApiException
     */
    public function create(CreateBadgeRequest $request, ?RequestOptions $options = null): Badge
    {
        $body = $this->transport->normalize($request);
        $data = $this->request('POST', $this->path('/badge'), $body, $options);

        return $this->transport->denormalize($data, Badge::class);
    }

    /**
     * PATCH /badge/{id}
     *
     * @throws HyvorApiException
     */
    public function update(int $id, UpdateBadgeRequest $request, ?RequestOptions $options = null): Badge
    {
        $body = $this->transport->normalize($request);
        $data = $this->request('PATCH', $this->path("/badge/{$id}"), $body, $options);

        return $this->transport->denormalize($data, Badge::class);
    }

    /**
     * DELETE /badge/{id}
     *
     * @throws HyvorApiException
     */
    public function delete(int $id, ?RequestOptions $options = null): void
    {
        $this->request('DELETE', $this->path("/badge/{$id}"), null, $options);
    }
}
