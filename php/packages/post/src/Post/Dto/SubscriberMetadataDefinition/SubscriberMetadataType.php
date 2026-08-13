<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\SubscriberMetadataDefinition;

/**
 * Only `TEXT` is currently supported by the API.
 */
enum SubscriberMetadataType: string
{
    case TEXT = 'text';
}
