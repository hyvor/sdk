<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\Subscriber;

/**
 * Whether `SubscribersResource::createOrUpdate()`'s `metadata` key overwrites
 * or merges into the subscriber's existing metadata, when updating an
 * existing subscriber.
 */
enum MetadataStrategy: string
{
    case MERGE = 'merge';
    case OVERWRITE = 'overwrite';
}
