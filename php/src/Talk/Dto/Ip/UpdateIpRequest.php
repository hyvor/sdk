<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Ip;

use Hyvor\Sdk\Talk\Dto\User\UserState;

final class UpdateIpRequest
{
    public function __construct(
        public readonly ?UserState $state = null,
        public readonly ?string $note = null,
        public readonly ?int $state_ends_at = null,
    ) {
    }
}
