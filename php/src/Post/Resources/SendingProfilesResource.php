<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Post\Dto\SendingProfile\CreateSendingProfileRequest;
use Hyvor\Sdk\Post\Dto\SendingProfile\SendingProfile;
use Hyvor\Sdk\Post\Dto\SendingProfile\UpdateSendingProfileRequest;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->post->newsletter($newsletterId)->sendingProfiles`
 */
final class SendingProfilesResource extends NewsletterScopedResource
{
    /**
     * GET /sending-profiles
     *
     * @return SendingProfile[]
     * @throws HyvorApiException
     */
    public function list(?RequestOptions $options = null): array
    {
        $data = $this->request('GET', $this->path('/sending-profiles'), null, $options);

        return $this->transport->denormalizeList($data, SendingProfile::class);
    }

    /**
     * POST /sending-profiles
     *
     * @throws HyvorApiException
     */
    public function create(CreateSendingProfileRequest $request, ?RequestOptions $options = null): SendingProfile
    {
        $body = $this->transport->normalize($request);
        $data = $this->request('POST', $this->path('/sending-profiles'), $body, $options);

        return $this->transport->denormalize($data, SendingProfile::class);
    }

    /**
     * PATCH /sending-profiles/{id}
     *
     * @throws HyvorApiException
     */
    public function update(int $id, UpdateSendingProfileRequest $request, ?RequestOptions $options = null): SendingProfile
    {
        $body = $this->transport->normalize($request);
        $data = $this->request('PATCH', $this->path("/sending-profiles/{$id}"), $body, $options);

        return $this->transport->denormalize($data, SendingProfile::class);
    }

    /**
     * DELETE /sending-profiles/{id}
     *
     * @throws HyvorApiException
     */
    public function delete(int $id, ?RequestOptions $options = null): void
    {
        $this->request('DELETE', $this->path("/sending-profiles/{$id}"), null, $options);
    }
}
