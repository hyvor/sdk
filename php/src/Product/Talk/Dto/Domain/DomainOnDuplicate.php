<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Dto\Domain;

enum DomainOnDuplicate: string
{
    case THROW = 'throw';
    case IGNORE = 'ignore';
}
