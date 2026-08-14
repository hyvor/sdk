<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto;

final class Domain
{
    public function __construct(
        public readonly int $id,
        public readonly int $created_at,
        public readonly string $domain,
        public readonly string $dkim_public_key,
        public readonly string $dkim_txt_name,
        public readonly string $dkim_txt_value,
        public readonly RelayDomainStatus $relay_status,
        public readonly ?int $relay_last_checked_at,
        public readonly ?string $relay_error_message,
    ) {
    }
}
