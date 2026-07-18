<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Website;

enum ColorTheme: string
{
    case LIGHT = 'light';
    case DARK = 'dark';
    case OS = 'os';
}
