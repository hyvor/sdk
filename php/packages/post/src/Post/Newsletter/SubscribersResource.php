<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Newsletter;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Post\Dto\Subscriber;
use Hyvor\Sdk\Post\Newsletter;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->newsletter($newsletterId)->subscribers`
 */
final class SubscribersResource
{
    public function __construct(private readonly Newsletter $client)
    {
    }

    /**
     * GET /api/console/subscribers
     *
     * @return Subscriber[]
     *
     * @throws HyvorApiException
     */
    public function list(?RequestOptions $options = null): array
    {
        $result = $this->client->request('GET', $this->client->path('/api/console/subscribers'), null, $options);

        return $this->client->transport->denormalizeList($result, Subscriber::class);
    }

    /**
     * POST /api/console/subscribers
     *
     * @param array{
     *     email: string,
     *     lists?: list<int|string>|array<string, int|string>|null,
     *     status?: 'subscribed'|'pending'|null,
     *     source?: 'console'|'form'|'import'|null,
     *     subscribe_ip?: string|null,
     *     subscribed_at?: int|null,
     *     metadata?: array<string, bool|float|int|string>|null,
     *     lists_strategy?: 'merge'|'overwrite'|'remove',
     *     list_skip_resubscribe_on?: list<string>|array<string, string>,
     *     list_removal_reason?: 'unsubscribe'|'bounce'|'complaint'|'other',
     *     metadata_strategy?: 'overwrite'|'merge',
     *     send_pending_confirmation_email?: bool,
     *     subscribeIp?: string|null,
     *     subscribedAt?: string|null,
     *     listSkipResubscribeOn: list<'unsubscribe'|'bounce'|'complaint'|'other'>|array<string, 'unsubscribe'|'bounce'|'complaint'|'other'>,
     *     listResubscribeOnValues: list<string>|array<string, string>,
     * } $data
     *
     * @throws HyvorApiException
     */
    public function create(array $data, ?RequestOptions $options = null): Subscriber
    {
        $result = $this->client->request('POST', $this->client->path('/api/console/subscribers'), $data, $options);

        return $this->client->transport->denormalize($result, Subscriber::class);
    }

    /**
     * GET /api/console/subscribers/email/{email}
     *
     * @throws HyvorApiException
     */
    public function getbyemail(string $email, ?RequestOptions $options = null): Subscriber
    {
        $result = $this->client->request('GET', $this->client->path('/api/console/subscribers/email/' . rawurlencode($email)), null, $options);

        return $this->client->transport->denormalize($result, Subscriber::class);
    }

    /**
     * POST /api/console/subscribers/{id}/resend-opt-in
     *
     * @throws HyvorApiException
     */
    public function resendoptin(int $id, ?RequestOptions $options = null): void
    {
        $this->client->request('POST', $this->client->path('/api/console/subscribers/' . $id . '/resend-opt-in'), null, $options);
    }

    /**
     * DELETE /api/console/subscribers/{id}
     *
     * @throws HyvorApiException
     */
    public function delete(int $id, ?RequestOptions $options = null): void
    {
        $this->client->request('DELETE', $this->client->path('/api/console/subscribers/' . $id), null, $options);
    }

    /**
     * POST /api/console/subscribers/bulk
     *
     * @param array{
     *     subscribers_ids: list<int>|array<string, int>,
     *     action: 'delete'|'metadata_update'|'status_change',
     *     status?: string|null,
     *     metadata: array<string, string>,
     * } $data
     *
     * @return array<mixed>
     *
     * @throws HyvorApiException
     */
    public function bulk(array $data, ?RequestOptions $options = null): array
    {
        return $this->client->request('POST', $this->client->path('/api/console/subscribers/bulk'), $data, $options);
    }
}
