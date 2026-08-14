<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto;

final class Subscriber
{
    public function __construct(
        public readonly int $id,
        public readonly string $email,
        public readonly SubscriberSource $source,
        public readonly SubscriberStatus $status,
        /** @var int[]|array<string, mixed> */
        public readonly array $list_ids,
        /** @var string[]|array<string, mixed> */
        public readonly array $lists,
        public readonly ?string $subscribe_ip,
        public readonly ?int $subscribed_at,
        /** @var array<string, mixed> */
        public readonly array $metadata,
    ) {
    }
}
