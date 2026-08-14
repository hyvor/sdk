<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto;

enum RelayDomainStatus: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case WARNING = 'warning';
    case SUSPENDED = 'suspended';
}
