<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Comment;

/**
 * Input for `CommentsResource::vote()` (`POST /comment/{id}/vote`). Unlike
 * most request DTOs, `type: null` here is a meaningful instruction (remove
 * the vote), not "leave unchanged" — so `CommentsResource::vote()` sends it
 * verbatim instead of going through `Transport::normalize()`'s
 * skip-null-values behavior. To remove a vote by a known user instead, see
 * `deleteVote()`.
 */
final class VoteOnCommentRequest
{
    public function __construct(
        public readonly ?VoteDirection $type,
        public readonly ?string $user_sso_id = null,
    ) {
    }
}
