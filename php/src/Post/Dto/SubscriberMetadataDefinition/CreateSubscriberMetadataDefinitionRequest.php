<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\SubscriberMetadataDefinition;

/**
 * Input for `SubscriberMetadataDefinitionsResource::create()`
 * (`POST /subscriber-metadata-definitions`).
 */
final class CreateSubscriberMetadataDefinitionRequest
{
    public function __construct(
        /**
         * Max length: 255. Can only contain lowercase letters, numbers, and
         * underscores. Cannot be changed once created.
         */
        public readonly string $key,
        /** Max length: 255. */
        public readonly string $name,
    ) {
    }
}
