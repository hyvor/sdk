<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Website;

use Hyvor\Sdk\Talk\Dto\Domain\DomainObject;

/**
 * Represents a Hyvor Talk website, as returned by
 * `POST /api/console/v1/websites` (see
 * https://talk.hyvor.com/docs/api-console#website-object).
 */
final class WebsiteObject
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly int $organization_id,
        public readonly int $owner_id,
        public readonly string $created_at,
        public readonly bool $is_blocked,
        public readonly bool $is_deleted,
        /** @var array<string, mixed>|null */
        public readonly ?array $metadata,
        public readonly ?string $created_by_source,
        /** @var DomainObject[] */
        public readonly array $domains,
    ) {
    }
}
