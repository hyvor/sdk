<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Job;

enum ExportFormat: string
{
    case HYVOR_TALK_JSON = 'hyvor_talk_json';
    case WORDPRESS_XML = 'wordpress_xml';
    case NEWSLETTER_SUBSCRIBERS_CSV = 'newsletter_subscribers_csv';
}
