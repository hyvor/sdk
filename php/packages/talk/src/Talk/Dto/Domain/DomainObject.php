<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Domain;

/**
 * Represents a domain attached to a Hyvor Talk website, as returned by
 * `POST /api/console/v1/{websiteId}/domains` (see
 * https://talk.hyvor.com/docs/api-console#domain-object).
 */
final class DomainObject
{
    public function __construct(
        public readonly int $id,
        public readonly Domain $domain,
    ) {
    }
}
