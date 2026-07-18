<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Website;

enum AuthSsoType: string
{
    case STATELESS = 'stateless';
    case OPENID = 'openid';
}
