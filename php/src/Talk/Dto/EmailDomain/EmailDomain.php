<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\EmailDomain;

final class EmailDomain
{
    public function __construct(
        public readonly int $id,
        public readonly string $domain,
        public readonly string $dkim_public_key,
        public readonly string $dkim_txt_name,
        public readonly string $dkim_txt_value,
        public readonly bool $verified,
        public readonly bool $verified_in_ses,
        public readonly bool $requested_by_current_website,
    ) {
    }
}
