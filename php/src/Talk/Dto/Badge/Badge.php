<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Badge;

final class Badge
{
    public function __construct(
        public readonly int $id,
        public readonly string $text,
        public readonly string $background_color,
        public readonly string $color,
        public readonly ?string $icon_url,
    ) {
    }
}
