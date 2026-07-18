<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Page;

enum PageFilter: string
{
    case OPEN = 'open';
    case CLOSED = 'closed';
    case PREMODERATION_ON = 'premoderation_on';
}
