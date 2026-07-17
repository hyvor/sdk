<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Website;

enum DisplayRepliedToType: string
{
    case None = 'none';
    case Deep = 'deep';
    case All = 'all';
}
