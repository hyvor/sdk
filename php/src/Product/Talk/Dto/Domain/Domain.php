<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Dto\Domain;

final class Domain
{
    public function __construct(
        public readonly int $id,
        public readonly string $domain,
    ) {
    }
}
