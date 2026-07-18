<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\SendingProfile;

/**
 * Input for `SendingProfilesResource::create()` (`POST /sending-profiles`).
 */
final class CreateSendingProfileRequest
{
    public function __construct(
        public readonly string $from_email,
        public readonly ?string $from_name = null,
        public readonly ?string $reply_to_email = null,
        public readonly ?string $brand_name = null,
        /** A publicly accessible URL of the logo. */
        public readonly ?string $brand_logo = null,
        public readonly ?string $brand_url = null,
    ) {
    }
}
