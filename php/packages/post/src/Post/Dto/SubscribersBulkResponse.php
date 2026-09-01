<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto;

final class SubscribersBulkResponse
{
    public function __construct(
        public readonly string $status,
        public readonly string $message,
        /** @var Subscriber[] */
        public readonly array $subscribers,
    ) {
    }
}
