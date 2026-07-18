<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Page;

final class PageRatingsSummary
{
    /**
     * `int|float` rather than `float`: PHP's `json_encode(0.0)` (and other
     * whole-number floats) serializes as a JSON integer, which Symfony's
     * serializer then refuses to widen to `float` under strict typing.
     */
    public function __construct(
        public readonly int|float $average,
        public readonly int $count,
    ) {
    }
}
