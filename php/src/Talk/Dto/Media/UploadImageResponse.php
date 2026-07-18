<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Media;

final class UploadImageResponse
{
    public function __construct(
        public readonly string $url,
    ) {
    }
}
