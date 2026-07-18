<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Ip;

use Hyvor\Sdk\Talk\Dto\User\UserState;

final class IP
{
    public function __construct(
        public readonly string $ip,
        public readonly ?string $note,
        public readonly UserState $state,
        public readonly ?int $state_ends_at,
    ) {
    }
}
