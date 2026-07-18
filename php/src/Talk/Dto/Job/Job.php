<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Job;

final class Job
{
    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed>|null $result
     */
    public function __construct(
        public readonly int $id,
        public readonly int $created_at,

        public readonly ?int $started_at,
        public readonly ?int $completed_at,
        public readonly ?int $failed_at,

        public readonly array $data,
        public readonly ?array $result,
        public readonly JobType $type,
        public readonly JobStatus $status,
        public readonly ?string $error,
    ) {
    }
}
