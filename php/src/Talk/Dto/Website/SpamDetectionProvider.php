<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Website;

enum SpamDetectionProvider: string
{
    case None = 'none';
    case Akismet = 'akismet';
    case Fortguard = 'fortguard';
}
