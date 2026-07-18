<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\EmailDomain;

final class VerifyEmailDomainResponse
{
    public function __construct(
        public readonly EmailDomainVerificationResult $data,
        public readonly EmailDomain $domain,
    ) {
    }
}
