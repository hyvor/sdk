<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\Template;

/**
 * Input for `TemplatesResource::update()` (`PATCH /templates`).
 */
final class UpdateTemplateRequest
{
    public function __construct(
        public readonly ?string $template = null,
    ) {
    }
}
