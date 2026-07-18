<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Post\Dto\Subscriber\BulkSubscriberAction;
use Hyvor\Sdk\Post\Dto\Subscriber\BulkUpdateSubscribersResponse;
use Hyvor\Sdk\Post\Dto\Subscriber\ListRemovalReason;
use Hyvor\Sdk\Post\Dto\Subscriber\ListSkipResubscribeReason;
use Hyvor\Sdk\Post\Dto\Subscriber\ListsStrategy;
use Hyvor\Sdk\Post\Dto\Subscriber\MetadataStrategy;
use Hyvor\Sdk\Post\Dto\Subscriber\Subscriber;
use Hyvor\Sdk\Post\Dto\Subscriber\SubscriberSource;
use Hyvor\Sdk\Post\Dto\Subscriber\SubscriberStatus;
use Hyvor\Sdk\Post\Dto\Subscriber\SubscriberStatusFilter;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->post->newsletter($newsletterId)->subscribers`
 */
final class SubscribersResource extends NewsletterScopedResource
{
    /**
     * GET /subscribers
     *
     * All keys are optional; omitted keys mean "no filter"/"use the API's
     * default".
     *
     * @param array{
     *     limit?: int,
     *     offset?: int,
     *     status?: SubscriberStatusFilter|string,
     *     list_id?: int,
     *     search?: string,
     * } $data search searches by email.
     * @return Subscriber[]
     * @throws HyvorApiException
     */
    public function list(array $data = [], ?RequestOptions $options = null): array
    {
        $result = $this->request('GET', $this->path('/subscribers'), $data, $options);

        return $this->transport->denormalizeList($result, Subscriber::class);
    }

    /**
     * GET /subscribers/email/{email}
     *
     * @throws HyvorApiException Including `NotFoundException` if no
     *  subscriber has this email.
     */
    public function getByEmail(string $email, ?RequestOptions $options = null): Subscriber
    {
        $data = $this->request('GET', $this->path('/subscribers/email/' . rawurlencode($email)), null, $options);

        return $this->transport->denormalize($data, Subscriber::class);
    }

    /**
     * POST /subscribers
     *
     * Creates a new subscriber, or updates the existing one if a subscriber
     * with the given email already exists.
     *
     * @param array{
     *     email: string,
     *     lists?: (int|string)[],
     *     status?: SubscriberStatus|string,
     *     source?: SubscriberSource|string,
     *     subscribe_ip?: string,
     *     subscribed_at?: int,
     *     metadata?: array<string, string>,
     *     lists_strategy?: ListsStrategy|string,
     *     list_skip_resubscribe_on?: (ListSkipResubscribeReason|string)[],
     *     list_removal_reason?: ListRemovalReason|string,
     *     metadata_strategy?: MetadataStrategy|string,
     *     send_pending_confirmation_email?: bool,
     * } $data lists: an array of list IDs or names; subscribes to or
     *  unsubscribes from lists based on `lists_strategy`. status: default
     *  subscribed. source: default console. subscribed_at: Unix timestamp of
     *  when the subscriber opted in; if not set, it will be set to the
     *  current time if status is `subscribed`. metadata: keys must be
     *  defined in the Subscriber Metadata Definitions section (or via the
     *  API). lists_strategy: default merge. list_skip_resubscribe_on: if the
     *  subscriber was previously removed from a list, the reason(s) for
     *  ignoring re-subscription to that list, default
     *  `[unsubscribe, bounce, complaint]`. list_removal_reason: default
     *  unsubscribe, only used when `lists_strategy` is `remove`.
     *  metadata_strategy: default merge. send_pending_confirmation_email:
     *  whether to send a confirmation email when adding a subscriber with
     *  `pending` status, or when changing an existing subscriber's status to
     *  `pending`, default false.
     * @throws HyvorApiException
     */
    public function createOrUpdate(array $data, ?RequestOptions $options = null): Subscriber
    {
        $result = $this->request('POST', $this->path('/subscribers'), $data, $options);

        return $this->transport->denormalize($result, Subscriber::class);
    }

    /**
     * DELETE /subscribers/{id}
     *
     * @throws HyvorApiException
     */
    public function delete(int $id, ?RequestOptions $options = null): void
    {
        $this->request('DELETE', $this->path("/subscribers/{$id}"), null, $options);
    }

    /**
     * POST /subscribers/bulk
     *
     * @param array{
     *     subscribers_ids: int[],
     *     action: BulkSubscriberAction|string,
     *     status?: SubscriberStatusFilter|string,
     *     metadata?: array<string, string>,
     * } $data status is required if `action` is `status_change`. metadata is
     *  required if `action` is `metadata_update`.
     * @throws HyvorApiException
     */
    public function bulkUpdate(
        array $data,
        ?RequestOptions $options = null,
    ): BulkUpdateSubscribersResponse {
        $result = $this->request('POST', $this->path('/subscribers/bulk'), $data, $options);

        return $this->transport->denormalize($result, BulkUpdateSubscribersResponse::class);
    }
}
