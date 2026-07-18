<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Sso;

/**
 * `search` matches by name, email, or the SSO ID given at creation.
 */
final class ListSsoUsersRequest
{
    public function __construct(
        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
        public readonly ?string $search = null,
    ) {
    }
}
