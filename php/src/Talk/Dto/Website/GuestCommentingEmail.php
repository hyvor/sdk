<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Website;

enum GuestCommentingEmail: string
{
    case NO = 'no';
    case OPTIONAL = 'optional';
    case REQUIRED = 'required';
}
