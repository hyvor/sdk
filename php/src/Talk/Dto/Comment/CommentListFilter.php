<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Comment;

enum CommentListFilter: string
{
    case UNREAD = 'unread';
    case UNREPLIED = 'unreplied';
    case GUEST = 'guest';
    case HAS_QUESTIONS = 'has_questions';
    case HAS_LINKS = 'has_links';
    case HAS_MEDIA = 'has_media';
}
