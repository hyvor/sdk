<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\EmailLog;

use Hyvor\Sdk\Talk\Dto\User\LoggedInUser;

final class EmailLog
{
    public function __construct(
        public readonly int $id,
        public readonly int $created_at,
        public readonly int $comment_id,
        public readonly EmailLogType $type,
        public readonly string $comment_url,
        public readonly ?LoggedInUser $user,
        public readonly ?string $guest_email,
    ) {
    }
}
