<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\Media;

enum MediaFolder: string
{
    case ISSUE_IMAGES = 'issue_images';
    case NEWSLETTER_IMAGES = 'newsletter_images';
    case IMPORT = 'import';
    case EXPORT = 'export';
}
