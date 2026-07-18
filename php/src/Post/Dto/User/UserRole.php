<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\User;

/**
 * A `UserInvite`'s role is always `ADMIN`; `User::$role` can additionally be
 * `OWNER`.
 */
enum UserRole: string
{
    case OWNER = 'owner';
    case ADMIN = 'admin';
}
