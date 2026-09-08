<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Relay\Dto;

final class Domain
{
    public function __construct(
        public readonly int $id,
        public readonly int $created_at,
        public readonly string $domain,
        public readonly DomainStatus $status,
        public readonly int $status_changed_at,
        public readonly string $dkim_selector,
        public readonly string $dkim_host,
        public readonly string $dkim_public_key,
        public readonly string $dkim_txt_value,
        public readonly ?int $dkim_checked_at,
        public readonly ?string $dkim_error_message,
    ) {
    }
}
