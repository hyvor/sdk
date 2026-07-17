<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Website;

enum AuthSsoType: string
{
    case Stateless = 'stateless';
    case Openid = 'openid';
}
