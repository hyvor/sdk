<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Dto\Job;

final class ListJobsRequest
{
    public function __construct(
        public readonly ?JobType $type = null,
    ) {
    }
}
