<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Relay\Dto;

final class ProjectUser
{
    public function __construct(
        public readonly int $id,
        public readonly int $created_at,
        /** @var string[] */
        public readonly array $scopes,
        public readonly ProjectUserMini $user,
        public readonly ?string $oidc_sub,
        public readonly Project $project,
    ) {
    }
}
