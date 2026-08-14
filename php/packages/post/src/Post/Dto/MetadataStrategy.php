<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto;

enum MetadataStrategy: string
{
    case OVERWRITE = 'overwrite';
    case MERGE = 'merge';
}
