<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Comment;

enum VoteDirection: string
{
    case UP = 'up';
    case DOWN = 'down';
}
