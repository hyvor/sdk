<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\Subscriber;

/**
 * How `SubscribersResource::createOrUpdate()`'s `lists` key is processed
 * when updating an existing subscriber's list subscriptions.
 */
enum ListsStrategy: string
{
    /** Merges the given lists into the subscriber's current lists. Default. */
    case MERGE = 'merge';
    /** Overwrites the subscriber's lists with the given ones. */
    case OVERWRITE = 'overwrite';
    /** Removes the subscriber from the given lists. */
    case REMOVE = 'remove';
}
