<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto;

enum SubscriberStatus: string
{
    case SUBSCRIBED = 'subscribed';
    case PENDING = 'pending';
}
