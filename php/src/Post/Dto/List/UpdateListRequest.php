<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\List;

/**
 * Input for `ListsResource::update()` (`PATCH /lists/{id}`). Every property
 * is optional; fields left null are not sent to the API and are left
 * unchanged (see `Transport::normalize()`).
 */
final class UpdateListRequest
{
    public function __construct(
        /** Max length: 255. */
        public readonly ?string $name = null,
        public readonly ?string $description = null,
    ) {
    }
}
