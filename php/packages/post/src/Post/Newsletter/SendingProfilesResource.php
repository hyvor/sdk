<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Newsletter;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Post\Dto\SendingProfile;
use Hyvor\Sdk\Post\Newsletter;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->newsletter($newsletterId)->sending_profiles`
 */
final class SendingProfilesResource
{
    public function __construct(private readonly Newsletter $client)
    {
    }

    /**
     * GET /api/console/sending-profiles
     *
     * @return SendingProfile[]
     *
     * @throws HyvorApiException
     */
    public function list(?RequestOptions $options = null): array
    {
        $result = $this->client->request('GET', $this->client->path('/api/console/sending-profiles'), null, $options);

        return $this->client->transport->denormalizeList($result, SendingProfile::class);
    }

    /**
     * POST /api/console/sending-profiles
     *
     * @param array{
     *     from_email: string,
     *     from_name?: string|null,
     *     reply_to_email?: string|null,
     *     brand_name?: string|null,
     *     brand_logo?: string|null,
     *     brand_url?: string|null,
     * } $data
     *
     * @throws HyvorApiException
     */
    public function create(array $data, ?RequestOptions $options = null): SendingProfile
    {
        $result = $this->client->request('POST', $this->client->path('/api/console/sending-profiles'), $data, $options);

        return $this->client->transport->denormalize($result, SendingProfile::class);
    }

    /**
     * DELETE /api/console/sending-profiles/{id}
     *
     * @return SendingProfile[]
     *
     * @throws HyvorApiException
     */
    public function delete(int $id, ?RequestOptions $options = null): array
    {
        $result = $this->client->request('DELETE', $this->client->path('/api/console/sending-profiles/' . $id), null, $options);

        return $this->client->transport->denormalizeList($result, SendingProfile::class);
    }

    /**
     * PATCH /api/console/sending-profiles/{id}
     *
     * @param array{
     *     from_email: string,
     *     from_name?: string|null,
     *     reply_to_email?: string|null,
     *     brand_name?: string|null,
     *     brand_logo?: string|null,
     *     brand_url?: string|null,
     *     is_default: bool,
     * } $data
     *
     * @throws HyvorApiException
     */
    public function update(int $id, array $data, ?RequestOptions $options = null): SendingProfile
    {
        $result = $this->client->request('PATCH', $this->client->path('/api/console/sending-profiles/' . $id), $data, $options);

        return $this->client->transport->denormalize($result, SendingProfile::class);
    }
}
