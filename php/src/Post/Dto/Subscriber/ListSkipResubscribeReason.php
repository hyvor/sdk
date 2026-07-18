<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\Subscriber;

/**
 * Used in `SubscribersResource::createOrUpdate()`'s
 * `list_skip_resubscribe_on` key: the removal reason(s) for which a previous
 * removal from a list should block re-subscription to that list. Pass an
 * empty array to force re-adding regardless of any previous removal reason.
 */
enum ListSkipResubscribeReason: string
{
    case UNSUBSCRIBE = 'unsubscribe';
    case BOUNCE = 'bounce';
    case COMPLAINT = 'complaint';
    case AUTO = 'auto';
}
