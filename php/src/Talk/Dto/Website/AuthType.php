<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Website;

enum AuthType: string
{
    case HYVOR = 'hyvor';
    case SSO = 'sso';
}
