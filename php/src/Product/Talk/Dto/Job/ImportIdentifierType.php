<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Dto\Job;

enum ImportIdentifierType: string
{
    case POST_ID = 'post_id';
    case RELATIVE_PATH = 'relative_path';
    case ABSOLUTE_URL = 'absolute_url';
}
