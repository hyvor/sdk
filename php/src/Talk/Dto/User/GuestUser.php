<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\User;

/**
 * The API's GuestUser object always has `type: null`, used only to
 * discriminate it from LoggedInUser (see CommentingUserDenormalizer). It
 * carries no information, so it is omitted here.
 */
final class GuestUser implements CommentingUser
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $email,
        public readonly ?string $picture_url,
    ) {
    }
}
