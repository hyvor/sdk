<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Reaction;

enum ReactionType: string
{
    case SUPERB = 'superb';
    case LOVE = 'love';
    case WOW = 'wow';
    case SAD = 'sad';
    case LAUGH = 'laugh';
    case ANGRY = 'angry';
}
