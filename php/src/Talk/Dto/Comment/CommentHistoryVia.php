<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Comment;

enum CommentHistoryVia: string
{
    case EMAIL = 'email';
    case SLACK = 'slack';
}
