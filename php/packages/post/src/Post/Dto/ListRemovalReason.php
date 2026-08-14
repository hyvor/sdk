<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto;

enum ListRemovalReason: string
{
    case UNSUBSCRIBE = 'unsubscribe';
    case BOUNCE = 'bounce';
    case COMPLAINT = 'complaint';
    case OTHER = 'other';
}
