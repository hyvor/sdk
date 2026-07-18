<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Website;

enum TopWidget: string
{
    case NONE = 'none';
    case REACTIONS = 'reactions';
    case RATINGS = 'ratings';
}
