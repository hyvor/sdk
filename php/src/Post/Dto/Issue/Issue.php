<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\Issue;

final class Issue
{
    public function __construct(
        public readonly int $id,
        public readonly string $uuid,
        public readonly int $created_at,
        public readonly ?string $subject,
        public readonly ?string $content,
        public readonly int $sending_profile_id,
        public readonly IssueStatus $status,
        /** @var int[] */
        public readonly array $lists,

        public readonly ?int $scheduled_at,
        public readonly ?int $sending_at,
        public readonly ?int $sent_at,
        public readonly int $total_sends,

        public readonly ?string $from_email,
        public readonly ?string $from_name,
        public readonly ?string $reply_to_email,

        public readonly int $sendable_subscribers_count,
    ) {
    }
}
