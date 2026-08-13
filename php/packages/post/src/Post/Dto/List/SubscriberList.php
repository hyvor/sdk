<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\List;

/**
 * Represents the API's `List` object (a list of subscribers). Named
 * `SubscriberList` here since `List` is a reserved word in PHP.
 */
final class SubscriberList
{
    public function __construct(
        public readonly int $id,
        public readonly int $created_at,
        public readonly string $name,
        public readonly ?string $description,
        public readonly int $subscribers_count,
    ) {
    }
}
