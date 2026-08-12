<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Dto\User;

enum UserType: string
{
    case HYVOR = 'hyvor';
    case SSO = 'sso';
}
