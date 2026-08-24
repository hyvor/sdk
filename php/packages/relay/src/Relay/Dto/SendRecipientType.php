<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Relay\Dto;

enum SendRecipientType: string
{
    case TO = 'to';
    case CC = 'cc';
    case BCC = 'bcc';
}
