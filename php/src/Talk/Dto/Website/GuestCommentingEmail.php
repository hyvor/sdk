<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Website;

enum GuestCommentingEmail: string
{
    case No = 'no';
    case Optional = 'optional';
    case Required = 'required';
}
