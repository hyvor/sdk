<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\EmailDomain;

final class EmailDomainVerificationResult
{
    public function __construct(
        public readonly bool $verified,
        public readonly ?EmailDomainVerificationDebug $debug,
    ) {
    }
}
