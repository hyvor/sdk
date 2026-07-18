<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\SubscriberMetadataDefinition;

/**
 * Input for `SubscriberMetadataDefinitionsResource::update()`
 * (`PATCH /subscriber-metadata-definitions/{id}`).
 */
final class UpdateSubscriberMetadataDefinitionRequest
{
    public function __construct(
        /** Max length: 255. */
        public readonly string $name,
    ) {
    }
}
