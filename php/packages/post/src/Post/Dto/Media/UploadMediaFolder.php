<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\Media;

/**
 * The subset of `MediaFolder` a caller may upload directly into via
 * `MediaResource::upload()`; `IMPORT` and `EXPORT` are set internally by the
 * API for generated files.
 */
enum UploadMediaFolder: string
{
    case ISSUE_IMAGES = 'issue_images';
    case NEWSLETTER_IMAGES = 'newsletter_images';
}
