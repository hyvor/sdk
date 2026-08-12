<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Dto\User;

final class UpdateEmailNotificationRequest
{
    public function __construct(
        public readonly ?bool $reply = null,
        public readonly ?bool $mention = null,
    ) {
    }
}
