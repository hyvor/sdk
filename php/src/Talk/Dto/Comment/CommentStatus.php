<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Comment;

enum CommentStatus: string
{
    case PUBLISHED = 'published';
    case PENDING = 'pending';
    case SPAM = 'spam';
    case DELETED = 'deleted';
}
