<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto;

final class IssuesGettestdataResponse
{
    public function __construct(
        /** @var string[] */
        public readonly array $verified_domains,
        /** @var string[] */
        public readonly array $suggested_emails,
        /** @var string[] */
        public readonly array $test_sent_emails,
    ) {
    }
}
