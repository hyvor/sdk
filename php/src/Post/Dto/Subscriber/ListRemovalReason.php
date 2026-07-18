<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\Subscriber;

/**
 * Used in `SubscribersResource::createOrUpdate()`'s `list_removal_reason`
 * key, when `lists_strategy` is `REMOVE`.
 */
enum ListRemovalReason: string
{
    /**
     * The subscriber explicitly asked to be removed from the list. Records
     * an unsubscription, blocking future re-adds unless the re-add request
     * sets `list_skip_resubscribe_on` to include `unsubscribe`.
     */
    case UNSUBSCRIBE = 'unsubscribe';
    case BOUNCE = 'bounce';
    /** Removes the subscriber from the list without recording an unsubscription. */
    case OTHER = 'other';
}
