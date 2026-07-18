<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\SendingProfile;

/**
 * Input for `SendingProfilesResource::update()` (`PATCH /sending-profiles/{id}`).
 * Every property is optional; fields left null are not sent to the API and
 * are left unchanged (see `Transport::normalize()`).
 */
final class UpdateSendingProfileRequest
{
    public function __construct(
        public readonly ?string $from_email = null,
        public readonly ?string $from_name = null,
        public readonly ?string $reply_to_email = null,
        public readonly ?string $brand_name = null,
        /** A publicly accessible URL of the logo. */
        public readonly ?string $brand_logo = null,
        public readonly ?string $brand_url = null,
        /** Only `true` is meaningful: makes this profile the default one. */
        public readonly ?true $is_default = null,
    ) {
    }
}
