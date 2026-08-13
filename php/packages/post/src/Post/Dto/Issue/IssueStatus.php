<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\Issue;

enum IssueStatus: string
{
    case DRAFT = 'draft';
    case SCHEDULED = 'scheduled';
    case SENDING = 'sending';
    case SENT = 'sent';
}
