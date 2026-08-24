<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Relay\Dto;

final class SendContent
{
    public function __construct(
        public readonly ?string $body_html,
        public readonly ?string $body_text,
        /** @var array<string, mixed> */
        public readonly array $headers,
        public readonly string $raw,
    ) {
    }
}
