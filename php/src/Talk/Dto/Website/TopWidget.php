<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Website;

enum TopWidget: string
{
    case None = 'none';
    case Reactions = 'reactions';
    case Ratings = 'ratings';
}
