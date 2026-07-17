<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Website;

enum NotifChannel: string
{
    case Email = 'email';
    case Slack = 'slack';
    case Off = 'off';
}
