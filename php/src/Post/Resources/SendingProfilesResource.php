<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Post\Dto\SendingProfile\SendingProfile;
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
     * @param array{
     *     from_email: string,
     *     from_name?: string,
     *     reply_to_email?: string,
     *     brand_name?: string,
     *     brand_logo?: string,
     *     brand_url?: string,
     * } $data brand_logo is a publicly accessible URL of the logo.
     * @throws HyvorApiException
     */
    public function create(array $data, ?RequestOptions $options = null): SendingProfile
    {
        $result = $this->request('POST', $this->path('/sending-profiles'), $data, $options);

        return $this->transport->denormalize($result, SendingProfile::class);
    }

    /**
     * PATCH /sending-profiles/{id}
     *
     * Every key is optional; keys left out of `$data` are left unchanged.
     *
     * @param array{
     *     from_email?: string,
     *     from_name?: string,
     *     reply_to_email?: string,
     *     brand_name?: string,
     *     brand_logo?: string,
     *     brand_url?: string,
     *     is_default?: true,
     * } $data brand_logo is a publicly accessible URL of the logo. is_default:
     *  only `true` is meaningful; makes this profile the default one.
     * @throws HyvorApiException
     */
    public function update(int $id, array $data, ?RequestOptions $options = null): SendingProfile
    {
        $result = $this->request('PATCH', $this->path("/sending-profiles/{$id}"), $data, $options);

        return $this->transport->denormalize($result, SendingProfile::class);
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
