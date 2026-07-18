<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\Issue;

/**
 * Input for `IssuesResource::list()` (`GET /issues`). Both properties are
 * optional; null means "use the API's default" and is not sent (see
 * `Transport::normalize()`).
 */
final class ListIssuesRequest
{
    public function __construct(
        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
    ) {
    }
}
