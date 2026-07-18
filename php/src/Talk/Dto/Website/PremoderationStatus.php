<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Website;

enum PremoderationStatus: string
{
    case OFF = 'off';
    case GUEST = 'guest';
    case GUEST_AND_NEW_COMMENTERS = 'guest_and_new_commenters';
    case ALL = 'all';
}
