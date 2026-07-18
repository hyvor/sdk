<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Comment;

enum CommentHistoryType: string
{
    case RULE = 'rule';
    case SPAM_DETECTION = 'spam_detection';
    case MODERATION = 'moderation';
    case EDIT = 'edit';
}
