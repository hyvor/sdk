<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\List;

/**
 * Input for `ListsResource::create()` (`POST /lists`).
 */
final class CreateListRequest
{
    public function __construct(
        /** Max length: 255. */
        public readonly string $name,
        public readonly ?string $description = null,
    ) {
    }
}
