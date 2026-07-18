<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\User;

/**
 * A comment, reaction, rating, vote, or flag can be made by either a
 * logged-in (HYVOR/SSO) user or a guest. The API discriminates the two by
 * the `type` field: null for guests, 'hyvor'/'sso' for logged-in users. See
 * `Hyvor\Sdk\Serialization\CommentingUserDenormalizer`.
 */
interface CommentingUser
{
}
