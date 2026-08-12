<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Dto\Comment;

enum CommentHistoryVia: string
{
    case EMAIL = 'email';
    case SLACK = 'slack';
}
