<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Dto\Domain;

use Hyvor\Sdk\Product\Talk\Dto\Website\Website;

/**
 * The domain entity embedded in a `DomainObject`.
 */
final class Domain
{
    public function __construct(
        public readonly int $id,
        public readonly int $websiteId,
        public readonly ?Website $website,
        public readonly string $domain,
        public readonly string $createdAt,
        public readonly string $updatedAt,
    ) {
    }
}
