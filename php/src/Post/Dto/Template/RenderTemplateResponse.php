<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\Template;

final class RenderTemplateResponse
{
    public function __construct(
        public readonly string $html,
    ) {
    }
}
