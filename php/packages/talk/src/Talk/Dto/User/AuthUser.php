<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\User;

/**
 * The Hyvor account user embedded in a `ModObject` (see
 * https://talk.hyvor.com/docs/api-console#mod-object).
 */
final class AuthUser
{
    public function __construct(
        public readonly int $id,
        public readonly string $username,
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $picture_url,
        public readonly ?string $location,
        public readonly ?string $bio,
        public readonly ?string $website_url,
        public readonly ?string $oidc_sub,
    ) {
    }
}
