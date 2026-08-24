<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Relay\Dto;

final class ProjectUserMini
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $username,
        public readonly ?string $picture_url,
        public readonly ?string $oidc_sub,
    ) {
    }
}
