<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Website;

enum RealtimeTyping: string
{
    case Off = 'off';
    case OnWithoutTyper = 'on_without_typer';
    case OnWithTyper = 'on_with_typer';
}
