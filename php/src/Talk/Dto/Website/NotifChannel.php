<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Website;

enum NotifChannel: string
{
    case EMAIL = 'email';
    case SLACK = 'slack';
    case OFF = 'off';
}
