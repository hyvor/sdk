<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Sso;

/**
 * Input for `SsoResource::createOrUpdate()` (`POST /sso/user`). `id` is the
 * user's ID in your system.
 */
final class UpsertSsoUserRequest
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $picture_url = null,
        public readonly ?string $title = null,
        public readonly ?string $website_url = null,
        public readonly ?string $bio = null,
        public readonly ?string $location = null,
    ) {
    }
}
