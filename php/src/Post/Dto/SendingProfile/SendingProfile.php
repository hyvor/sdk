<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\SendingProfile;

final class SendingProfile
{
    public function __construct(
        public readonly int $id,
        public readonly int $created_at,
        public readonly string $from_email,
        public readonly ?string $from_name,
        public readonly ?string $reply_to_email,
        public readonly ?string $brand_name,
        public readonly ?string $brand_logo,
        public readonly ?string $brand_url,
        public readonly bool $is_default,
        public readonly bool $is_system,
    ) {
    }
}
