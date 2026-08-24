<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Relay\Dto;

final class Send
{
    public function __construct(
        public readonly int $id,
        public readonly string $uuid,
        public readonly int $created_at,
        public readonly string $from_address,
        public readonly ?string $from_name,
        public readonly ?string $subject,
        public readonly int $size_bytes,
        public readonly bool $queued,
        public readonly int $send_after,
        /** @var SendRecipient[] */
        public readonly array $recipients,
        /** @var SendAttempt[] */
        public readonly array $attempts,
        /** @var SendFeedback[] */
        public readonly array $feedback,
    ) {
    }
}
