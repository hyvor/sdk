<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\EmailDomain;

final class CreateEmailDomainRequest
{
    public function __construct(
        public readonly string $domain,
    ) {
    }
}
