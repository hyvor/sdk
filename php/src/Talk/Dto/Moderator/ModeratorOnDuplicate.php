<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Moderator;

enum ModeratorOnDuplicate: string
{
    case THROW = 'throw';
    case IGNORE = 'ignore';
    case UPDATE = 'update';
}
