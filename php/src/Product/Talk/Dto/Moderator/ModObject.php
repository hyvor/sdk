<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Dto\Moderator;

use Hyvor\Sdk\Product\Talk\Dto\User\AuthUser;

/**
 * Represents a moderator of a Hyvor Talk website, as returned by
 * `POST /api/console/v1/{websiteId}/mods` (see
 * https://talk.hyvor.com/docs/api-console#mod-object).
 */
final class ModObject
{
    public function __construct(
        public readonly int $id,
        public readonly int $created_at,
        public readonly string $role,
        public readonly int $website_id,
        public readonly AuthUser $user,
    ) {
    }
}
