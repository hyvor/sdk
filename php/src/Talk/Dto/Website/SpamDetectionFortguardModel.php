<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Website;

enum SpamDetectionFortguardModel: string
{
    case GPT_4O_MINI = 'gpt-4o-mini';
    case CLAUDE_3_HAIKU = 'claude-3-haiku';
}
