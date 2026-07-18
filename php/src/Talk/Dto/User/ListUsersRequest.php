<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\User;

/**
 * Input for `UsersResource::list()` (`GET /users`). `search` matches by
 * name, exact HYVOR username, exact email (SSO users), or exact SSO ID.
 */
final class ListUsersRequest
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?int $badge_id = null,
        public readonly ?int $plan_id = null,
        public readonly ?string $last_ip_address = null,
        public readonly ?UserState $state = null,
        public readonly ?UserSort $sort = null,
        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
    ) {
    }
}
