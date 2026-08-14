<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto;

final class Template
{
    public function __construct(
        public readonly string $template,
    ) {
    }
}
