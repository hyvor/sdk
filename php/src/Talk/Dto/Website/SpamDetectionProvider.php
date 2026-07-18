<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Website;

enum SpamDetectionProvider: string
{
    case NONE = 'none';
    case AKISMET = 'akismet';
    case FORTGUARD = 'fortguard';
}
