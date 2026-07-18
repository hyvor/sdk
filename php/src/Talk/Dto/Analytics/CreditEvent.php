<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Analytics;

enum CreditEvent: string
{
    case EMBED_COMMENTS = 'embed_comments';
    case EMBED_GATED_CONTENT = 'embed_gated_content';
    case EMBED_COMMENT_COUNTS = 'embed_comment_counts';
    case EMBED_NEWSLETTER = 'embed_newsletter';
    case EMAIL_NEWSLETTER = 'email_newsletter';
    case SPAM_DETECTION_AKISMET = 'spam_detection_akismet';
    case SPAM_DETECTION_FORTGUARD = 'spam_detection_fortguard';
    case API_DATA = 'api_data';
}
