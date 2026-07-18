<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Website;

enum RealtimeTyping: string
{
    case OFF = 'off';
    case ON_WITHOUT_TYPER = 'on_without_typer';
    case ON_WITH_TYPER = 'on_with_typer';
}
