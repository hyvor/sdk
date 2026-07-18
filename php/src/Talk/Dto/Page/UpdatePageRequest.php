<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Page;

final class UpdatePageRequest
{
    public function __construct(
        public readonly ?bool $is_closed = null,
        public readonly ?bool $is_premoderation_on = null,
        public readonly ?string $author_email = null,
    ) {
    }
}
