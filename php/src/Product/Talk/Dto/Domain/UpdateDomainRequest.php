<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Dto\Domain;

final class UpdateDomainRequest
{
    public function __construct(
        public readonly string $old_domain,
        public readonly string $new_domain,
    ) {
    }
}
