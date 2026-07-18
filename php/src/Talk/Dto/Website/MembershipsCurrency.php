<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Website;

enum MembershipsCurrency: string
{
    case USD = 'usd';
    case EUR = 'eur';
    case GBP = 'gbp';
}
