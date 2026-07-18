<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\Subscriber;

final class Subscriber
{
    public function __construct(
        public readonly int $id,
        public readonly string $email,
        public readonly SubscriberSource $source,
        public readonly SubscriberStatus $status,
        /** @var int[] */
        public readonly array $list_ids,
        /** @var string[] List names. */
        public readonly array $lists,
        public readonly ?string $subscribe_ip,
        public readonly bool $is_opted_in,
        public readonly ?int $subscribed_at,
        /** @var array<string, string> */
        public readonly array $metadata,
    ) {
    }
}
