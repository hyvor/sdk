<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Comment;

enum CommentListType: string
{
    case PUBLISHED = 'published';
    case PENDING = 'pending';
    case DELETED = 'deleted';
    case SPAM = 'spam';
    case FLAGGED = 'flagged';
}
