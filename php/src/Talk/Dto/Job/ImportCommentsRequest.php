<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Job;

/**
 * Non-file fields for `JobsResource::importComments()`
 * (`POST /data/import/comments`); the file itself is passed separately as
 * an `UploadedFile`, since the request is `multipart/form-data`.
 */
final class ImportCommentsRequest
{
    public function __construct(
        public readonly ImportCommentsFormat $format,
        public readonly ?ImportIdentifierType $identifier_type = null,
    ) {
    }
}
