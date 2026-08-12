<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Dto\Website;

enum GuestCommentingEmail: string
{
    case NO = 'no';
    case OPTIONAL = 'optional';
    case REQUIRED = 'required';
}
