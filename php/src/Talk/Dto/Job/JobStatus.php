<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Job;

enum JobStatus: string
{
    case PENDING = 'pending';
    case RUNNING = 'running';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
}
