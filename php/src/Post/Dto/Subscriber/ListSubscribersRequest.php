<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\Subscriber;

/**
 * Input for `SubscribersResource::list()` (`GET /subscribers`). All
 * properties are optional; null means "no filter"/"use the API's default"
 * and is not sent (see `Transport::normalize()`).
 */
final class ListSubscribersRequest
{
    public function __construct(
        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
        public readonly ?SubscriberStatusFilter $status = null,
        public readonly ?int $list_id = null,
        /** Searches by email. */
        public readonly ?string $search = null,
    ) {
    }
}
