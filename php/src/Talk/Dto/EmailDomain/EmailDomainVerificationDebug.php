<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\EmailDomain;

final class EmailDomainVerificationDebug
{
    public function __construct(
        public readonly string $last_checked_at,
        public readonly string $error_type,
    ) {
    }
}
