<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Page;

/**
 * Input for `PagesResource::create()` (`POST /page`). If a page with the
 * same `identifier` already exists, it is updated with this data instead.
 */
final class CreatePageRequest
{
    public function __construct(
        public readonly string $identifier,
        public readonly string $url,
        public readonly ?string $title = null,
        public readonly ?string $author_email = null,
        public readonly ?int $created_at = null,
    ) {
    }
}
