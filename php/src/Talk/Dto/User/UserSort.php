<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\User;

enum UserSort: string
{
    case RECENTLY_COMMENTED = 'recently_commented';
    case MOST_COMMENTED = 'most_commented';
    case RECENTLY_SEEN = 'recently_seen';
    case RECENTLY_JOINED = 'recently_joined';
}
