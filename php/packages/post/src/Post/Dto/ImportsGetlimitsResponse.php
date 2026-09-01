<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto;

final class ImportsGetlimitsResponse
{
    public function __construct(
        public readonly bool $daily_limit_exceeded,
        public readonly bool $monthly_limit_exceeded,
    ) {
    }
}
