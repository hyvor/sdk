<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Relay\Dto;

final class SendFeedback
{
    public function __construct(
        public readonly int $id,
        public readonly int $created_at,
        public readonly SendFeedbackType $type,
        public readonly int $recipient_id,
        public readonly int $debug_incoming_email_id,
    ) {
    }
}
