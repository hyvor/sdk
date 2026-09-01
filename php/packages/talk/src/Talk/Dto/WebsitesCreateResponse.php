<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto;

final class WebsitesCreateResponse
{
    public function __construct(
        public readonly Website $website,
        public readonly ?Mod $mod,
    ) {
    }
}
