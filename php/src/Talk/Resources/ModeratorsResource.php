<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\RequestOptions;
use Hyvor\Sdk\Talk\Dto\Moderator\AddModeratorRequest;
use Hyvor\Sdk\Talk\Dto\Moderator\DeleteModeratorRequest;
use Hyvor\Sdk\Talk\Dto\Moderator\Mod;
use Hyvor\Sdk\Talk\Dto\Moderator\UpdateModeratorRequest;

/**
 * `$client->talk->website($websiteId)->moderators`
 */
final class ModeratorsResource extends WebsiteScopedResource
{
    /**
     * GET /moderators
     *
     * @return Mod[]
     * @throws HyvorApiException
     */
    public function list(?RequestOptions $options = null): array
    {
        $data = $this->request('GET', $this->path('/moderators'), null, $options);

        return $this->transport->denormalizeList($data, Mod::class);
    }

    /**
     * POST /moderators
     *
     * @throws HyvorApiException
     */
    public function create(AddModeratorRequest $request, ?RequestOptions $options = null): Mod
    {
        $body = $this->transport->normalize($request);
        $data = $this->request('POST', $this->path('/moderators'), $body, $options);

        return $this->transport->denormalize($data, Mod::class);
    }

    /**
     * PATCH /moderators
     *
     * @throws HyvorApiException
     */
    public function update(UpdateModeratorRequest $request, ?RequestOptions $options = null): Mod
    {
        $body = $this->transport->normalize($request);
        $data = $this->request('PATCH', $this->path('/moderators'), $body, $options);

        return $this->transport->denormalize($data, Mod::class);
    }

    /**
     * DELETE /moderators
     *
     * @throws HyvorApiException
     */
    public function delete(DeleteModeratorRequest $request, ?RequestOptions $options = null): void
    {
        $body = $this->transport->normalize($request);
        $this->request('DELETE', $this->path('/moderators'), $body, $options);
    }
}
