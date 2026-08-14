<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto;

final class SubscriberImport
{
    public function __construct(
        public readonly int $id,
        public readonly int $created_at,
        public readonly SubscriberImportStatus $status,
        /** @var array<string, mixed>|null */
        public readonly ?array $fields,
        /** @var string[]|null */
        public readonly ?array $csv_fields,
        public readonly ?int $imported_subscribers,
        public readonly ?string $warnings,
        public readonly ?string $error_message,
    ) {
    }
}
