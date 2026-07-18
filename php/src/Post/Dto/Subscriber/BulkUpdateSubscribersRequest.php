<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\Subscriber;

/**
 * Input for `SubscribersResource::bulkUpdate()` (`POST /subscribers/bulk`).
 */
final class BulkUpdateSubscribersRequest
{
    /**
     * @param int[] $subscribers_ids
     * @param SubscriberStatusFilter|null $status Required if `$action` is `STATUS_CHANGE`.
     * @param array<string, string>|null $metadata Required if `$action` is `METADATA_UPDATE`.
     */
    public function __construct(
        public readonly array $subscribers_ids,
        public readonly BulkSubscriberAction $action,
        public readonly ?SubscriberStatusFilter $status = null,
        public readonly ?array $metadata = null,
    ) {
    }
}
