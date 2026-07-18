<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Sso;

/**
 * Input for `SsoResource::delete()` (`DELETE /sso/user`). `id` is the
 * user's ID in your system. By default, only the user's profile data is
 * deleted (their comments show as "Anonymous user"); set `data: true` to
 * also delete their comments.
 */
final class DeleteSsoUserRequest
{
    public function __construct(
        public readonly string $id,
        public readonly ?bool $data = null,
    ) {
    }
}
