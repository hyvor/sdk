<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Dto\Moderator;

use Hyvor\Sdk\Product\Talk\Dto\Website\Website;

/**
 * The moderator entity embedded in a `Website`'s `mods` relation.
 */
final class Mod
{
    public function __construct(
        public readonly int|string $id,
        public readonly int|string $userId,
        public readonly string $role,
        public readonly int $websiteId,
        public readonly ?Website $website,
        public readonly string $createdAt,
    ) {
    }
}
