<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Domain;

final class CreateDomainsRequest
{
    /**
     * @param string[] $domains
     */
    public function __construct(
        public readonly array $domains,
        public readonly ?DomainOnDuplicate $on_duplicate = null,
    ) {
    }
}
