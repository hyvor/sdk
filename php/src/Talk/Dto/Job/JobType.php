<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Job;

enum JobType: string
{
    case IMPORT_COMMENTS = 'import_comments';
    case IMPORT_NEWSLETTER_SUBSCRIBERS = 'import_newsletter_subscribers';
    case EXPORT = 'export';
    case BULK_MODERATE_COMMENTS = 'bulk_moderate_comments';
}
