<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Domain;

final class DeleteDomainRequest
{
    public function __construct(
        public readonly string $domain,
    ) {
    }
}
