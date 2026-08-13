<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\Subscriber;

/**
 * The subscriber's status, as returned by the API and as settable via
 * `SubscribersResource::createOrUpdate()`. `SubscribersResource::list()` and
 * `SubscribersResource::bulkUpdate()` additionally accept `unsubscribed` as a
 * filter/bulk-action value, which is never returned on the `Subscriber`
 * object itself.
 */
enum SubscriberStatus: string
{
    case SUBSCRIBED = 'subscribed';
    case PENDING = 'pending';
}
