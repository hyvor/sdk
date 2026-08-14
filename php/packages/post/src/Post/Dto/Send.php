<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto;

final class Send
{
    public function __construct(
        public readonly int $id,
        public readonly int $created_at,
        public readonly ?Subscriber $subscriber,
        public readonly string $email,
        public readonly SendStatus $status,
        public readonly ?int $sent_at,
        public readonly ?int $failed_at,
        public readonly ?int $delivered_at,
        public readonly ?int $unsubscribed_at,
        public readonly ?int $bounced_at,
        public readonly bool $hard_bounce,
        public readonly ?int $complained_at,
    ) {
    }
}
