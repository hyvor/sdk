<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto;

final class IssuesPreviewResponse
{
    public function __construct(
        public readonly string $html,
        public readonly int $sendable_subscribers_count,
    ) {
    }
}
