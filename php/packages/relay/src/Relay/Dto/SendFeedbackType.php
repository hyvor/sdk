<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Relay\Dto;

enum SendFeedbackType: string
{
    case BOUNCE = 'bounce';
    case COMPLAINT = 'complaint';
}
