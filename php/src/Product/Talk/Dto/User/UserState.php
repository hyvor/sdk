<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Dto\User;

enum UserState: string
{
    case DEFAULT = 'default';
    case BANNED = 'banned';
    case SHADOWED = 'shadowed';
    case TRUSTED = 'trusted';
}
