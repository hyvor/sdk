<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\Template;

/**
 * Input for `TemplatesResource::render()` (`POST /templates/render`).
 */
final class RenderTemplateRequest
{
    public function __construct(
        public readonly ?string $template = null,
    ) {
    }
}
