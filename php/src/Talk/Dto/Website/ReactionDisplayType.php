<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Website;

enum ReactionDisplayType: string
{
    case Text = 'text';
    case Image = 'image';
    case Both = 'both';
}
