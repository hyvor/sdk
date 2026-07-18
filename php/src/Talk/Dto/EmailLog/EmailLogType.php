<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\EmailLog;

enum EmailLogType: string
{
    case REPLY = 'reply';
    case MENTION = 'mention';
    case MOD = 'mod';
    case AUTHOR = 'author';
}
