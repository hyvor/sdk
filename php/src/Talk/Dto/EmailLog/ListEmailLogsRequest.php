<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\EmailLog;

final class ListEmailLogsRequest
{
    public function __construct(
        public readonly ?EmailLogType $type = null,
        public readonly ?string $user_htid = null,
        public readonly ?string $user_sso_id = null,
        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
    ) {
    }
}
