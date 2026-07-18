<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\Subscriber;

/**
 * Used in `SubscribersResource::list()`'s and
 * `SubscribersResource::bulkUpdate()`'s `status` key. Broader than `SubscriberStatus`
 * (includes `unsubscribed`), since a subscriber can be filtered or bulk-moved
 * into that status even though it's never returned on the `Subscriber` object.
 */
enum SubscriberStatusFilter: string
{
    case SUBSCRIBED = 'subscribed';
    case UNSUBSCRIBED = 'unsubscribed';
    case PENDING = 'pending';
}
