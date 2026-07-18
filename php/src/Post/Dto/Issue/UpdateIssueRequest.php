<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\Issue;

/**
 * Input for `IssuesResource::update()` (`PATCH /issues/{id}`). Every
 * property is optional; fields left null are not sent to the API and are
 * left unchanged (see `Transport::normalize()`).
 */
final class UpdateIssueRequest
{
    public function __construct(
        public readonly ?string $subject = null,
        /** @var int[]|null */
        public readonly ?array $lists = null,
        public readonly ?string $content = null,
        public readonly ?int $sending_profile_id = null,
    ) {
    }
}
