<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\Subscriber;

final class BulkUpdateSubscribersResponse
{
    /**
     * @param Subscriber[] $subscribers
     */
    public function __construct(
        public readonly string $status,
        public readonly string $message,
        public readonly array $subscribers,
    ) {
    }
}
