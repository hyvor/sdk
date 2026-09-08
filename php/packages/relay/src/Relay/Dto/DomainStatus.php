<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Relay\Dto;

enum DomainStatus: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case WARNING = 'warning';
    case SUSPENDED = 'suspended';
}
