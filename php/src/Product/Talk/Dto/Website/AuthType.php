<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Dto\Website;

enum AuthType: string
{
    case HYVOR = 'hyvor';
    case SSO = 'sso';
}
