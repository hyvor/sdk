<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\User;

final class EmailNotificationStatus
{
    public function __construct(
        public readonly bool $reply,
        public readonly bool $mention,
    ) {
    }
}
