<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\Subscriber;

/**
 * The subscriber's status, as returned by the API and as settable via
 * `SubscribersResource::createOrUpdate()`. See `SubscriberStatusFilter` for
 * the broader set of statuses usable in filters and bulk actions.
 */
enum SubscriberStatus: string
{
    case SUBSCRIBED = 'subscribed';
    case PENDING = 'pending';
}
