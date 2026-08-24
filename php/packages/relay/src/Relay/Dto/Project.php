<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Relay\Dto;

final class Project
{
    public function __construct(
        public readonly int $id,
        public readonly int $created_at,
        public readonly string $name,
        public readonly ProjectSendType $send_type,
    ) {
    }
}
