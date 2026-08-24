<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Relay\Dto;

final class SendAttempt
{
    public function __construct(
        public readonly int $id,
        public readonly int $created_at,
        public readonly SendAttemptStatus $status,
        public readonly int $try_count,
        public readonly string $domain,
        /** @var string[] */
        public readonly array $resolved_mx_hosts,
        public readonly ?string $responded_mx_host,
        /** @var array<string, mixed> */
        public readonly array $smtp_conversations,
        public readonly int $duration_ms,
        /** @var SendAttemptRecipient[] */
        public readonly array $recipients,
    ) {
    }
}
