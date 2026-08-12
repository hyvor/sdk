<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Post\Dto\SubscriberMetadataDefinition;

/**
 * Only `TEXT` is currently supported by the API.
 */
enum SubscriberMetadataType: string
{
    case TEXT = 'text';
}
