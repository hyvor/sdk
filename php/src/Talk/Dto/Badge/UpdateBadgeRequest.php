<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Badge;

final class UpdateBadgeRequest
{
    public function __construct(
        public readonly ?string $text = null,
        public readonly ?string $color = null,
        public readonly ?string $background_color = null,
        public readonly ?string $icon_url = null,
    ) {
    }
}
