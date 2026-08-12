<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Dto\User;

enum UserRole: string
{
    case OWNER = 'owner';
    case MOD = 'mod';
    case ADMIN = 'admin';
}
