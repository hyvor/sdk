<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Relay\Dto;

final class SendRecipient
{
    public function __construct(
        public readonly int $id,
        public readonly SendRecipientType $type,
        public readonly string $address,
        public readonly string $name,
        public readonly SendRecipientStatus $status,
        public readonly int $try_count,
    ) {
    }
}
