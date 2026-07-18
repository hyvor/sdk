<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Job;

/**
 * Input for `JobsResource::export()` (`POST /data/export`). If `email` is
 * null, the website owner's email is used.
 */
final class ExportDataRequest
{
    public function __construct(
        public readonly ExportFormat $format,
        public readonly ?int $from = null,
        public readonly ?int $to = null,
        public readonly ?string $email = null,
    ) {
    }
}
