<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\Subscriber;

enum SubscriberSource: string
{
    case CONSOLE = 'console';
    case FORM = 'form';
    case IMPORT = 'import';
}
