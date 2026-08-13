<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto;

enum ListsStrategy: string
{
    case MERGE = 'merge';
    case OVERWRITE = 'overwrite';
    case REMOVE = 'remove';
}
