<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto;

final class IssuesSendtestResponse
{
    public function __construct(
        public readonly int $success_count,
    ) {
    }
}
