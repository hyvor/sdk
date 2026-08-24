<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Relay\Dto;

enum ProjectSendType: string
{
    case TRANSACTIONAL = 'transactional';
    case DISTRIBUTIONAL = 'distributional';
}
