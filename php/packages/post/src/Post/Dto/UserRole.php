<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto;

enum UserRole: string
{
    case OWNER = 'owner';
    case ADMIN = 'admin';
}
