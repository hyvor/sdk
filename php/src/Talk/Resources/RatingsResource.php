<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\RequestOptions;
use Hyvor\Sdk\Talk\Dto\Rating\ListRatingsRequest;
use Hyvor\Sdk\Talk\Dto\Rating\Rating;

/**
 * `$client->talk->website($websiteId)->ratings`
 */
final class RatingsResource extends WebsiteScopedResource
{
    /**
     * GET /ratings
     *
     * @return Rating[]
     * @throws HyvorApiException
     */
    public function list(?ListRatingsRequest $request = null, ?RequestOptions $options = null): array
    {
        $body = $this->transport->normalize($request ?? new ListRatingsRequest());
        $data = $this->request('GET', $this->path('/ratings'), $body, $options);

        return $this->transport->denormalizeList($data, Rating::class);
    }

    /**
     * DELETE /rating/{id}
     *
     * @throws HyvorApiException
     */
    public function delete(int $id, ?RequestOptions $options = null): void
    {
        $this->request('DELETE', $this->path("/rating/{$id}"), null, $options);
    }
}
