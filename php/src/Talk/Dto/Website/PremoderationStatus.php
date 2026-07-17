<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Website;

enum PremoderationStatus: string
{
    case Off = 'off';
    case Guest = 'guest';
    case GuestAndNewCommenters = 'guest_and_new_commenters';
    case All = 'all';
}
