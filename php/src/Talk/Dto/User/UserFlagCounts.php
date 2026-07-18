<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\User;

final class UserFlagCounts
{
    public function __construct(
        public readonly int $received,
        public readonly int $given,
    ) {
    }
}
