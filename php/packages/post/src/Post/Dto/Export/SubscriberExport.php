<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\Export;

final class SubscriberExport
{
    public function __construct(
        public readonly int $id,
        public readonly int $created_at,
        public readonly SubscriberExportStatus $status,
        public readonly ?string $error_message,
        public readonly ?string $url,
    ) {
    }
}
