<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Website;

enum MembershipsCurrency: string
{
    case Usd = 'usd';
    case Eur = 'eur';
    case Gbp = 'gbp';
}
