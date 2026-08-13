<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto;

final class SubscriberMetadataDefinition
{
    public function __construct(
        public readonly int $id,
        public readonly int $created_at,
        public readonly string $key,
        public readonly string $name,
        public readonly SubscriberMetadataDefinitionType $type,
    ) {
    }
}
