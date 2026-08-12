<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Dto\EmailDomain;

final class CreateEmailDomainRequest
{
    public function __construct(
        public readonly string $domain,
    ) {
    }
}
