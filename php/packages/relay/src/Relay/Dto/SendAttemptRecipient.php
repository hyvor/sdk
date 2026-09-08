<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Relay\Dto;

final class SendAttemptRecipient
{
    public function __construct(
        public readonly int $id,
        public readonly int $created_at,
        public readonly int $recipient_id,
        public readonly SendRecipientStatus $recipient_status,
        public readonly int $smtp_code,
        public readonly ?string $smtp_enhanced_code,
        public readonly string $smtp_message,
        public readonly bool $is_suppressed,
    ) {
    }
}
