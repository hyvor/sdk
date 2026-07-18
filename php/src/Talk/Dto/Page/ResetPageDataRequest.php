<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Page;

/**
 * Input for `PagesResource::reset()` (`POST /page/{id}/reset`). Resets the
 * data of the page (comments/reactions/ratings) you request. Use with
 * caution — there is no undo. Flags left false are not reset.
 */
final class ResetPageDataRequest
{
    public function __construct(
        public readonly bool $comments = false,
        public readonly bool $reactions = false,
        public readonly bool $ratings = false,
    ) {
    }
}
