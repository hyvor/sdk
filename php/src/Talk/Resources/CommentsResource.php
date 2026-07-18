<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\RequestOptions;
use Hyvor\Sdk\Talk\Dto\Comment\BulkModerateRequest;
use Hyvor\Sdk\Talk\Dto\Comment\Comment;
use Hyvor\Sdk\Talk\Dto\Comment\CreateCommentRequest;
use Hyvor\Sdk\Talk\Dto\Comment\CreateFlagRequest;
use Hyvor\Sdk\Talk\Dto\Comment\Flag;
use Hyvor\Sdk\Talk\Dto\Comment\GetFlagsRequest;
use Hyvor\Sdk\Talk\Dto\Comment\GetVotersRequest;
use Hyvor\Sdk\Talk\Dto\Comment\ListCommentsRequest;
use Hyvor\Sdk\Talk\Dto\Comment\MarkCommentsReadRequest;
use Hyvor\Sdk\Talk\Dto\Comment\ReplyToCommentRequest;
use Hyvor\Sdk\Talk\Dto\Comment\UnreadCommentCounts;
use Hyvor\Sdk\Talk\Dto\Comment\UpdateCommentRequest;
use Hyvor\Sdk\Talk\Dto\Comment\VoteOnCommentRequest;
use Hyvor\Sdk\Talk\Dto\User\LoggedInUser;

/**
 * `$client->talk->website($websiteId)->comments`
 */
final class CommentsResource extends WebsiteScopedResource
{
    /**
     * GET /comments
     *
     * @return Comment[]
     * @throws HyvorApiException
     */
    public function list(?ListCommentsRequest $request = null, ?RequestOptions $options = null): array
    {
        $body = $this->transport->normalize($request ?? new ListCommentsRequest());
        $data = $this->request('GET', $this->path('/comments'), $body, $options);

        return $this->transport->denormalizeList($data, Comment::class);
    }

    /**
     * GET /comments/unread-counts
     *
     * @throws HyvorApiException
     */
    public function unreadCounts(?RequestOptions $options = null): UnreadCommentCounts
    {
        $data = $this->request('GET', $this->path('/comments/unread-counts'), null, $options);

        return $this->transport->denormalize($data, UnreadCommentCounts::class);
    }

    /**
     * POST /comments/read
     *
     * @throws HyvorApiException
     */
    public function markRead(?MarkCommentsReadRequest $request = null, ?RequestOptions $options = null): void
    {
        $body = $this->transport->normalize($request ?? new MarkCommentsReadRequest());
        $this->request('POST', $this->path('/comments/read'), $body, $options);
    }

    /**
     * POST /comment
     *
     * @throws HyvorApiException
     */
    public function create(CreateCommentRequest $request, ?RequestOptions $options = null): Comment
    {
        $body = $this->transport->normalize($request);
        $data = $this->request('POST', $this->path('/comment'), $body, $options);

        return $this->transport->denormalize($data, Comment::class);
    }

    /**
     * GET /comment/{id}
     *
     * @throws HyvorApiException
     */
    public function get(int $id, ?RequestOptions $options = null): Comment
    {
        $data = $this->request('GET', $this->path("/comment/{$id}"), null, $options);

        return $this->transport->denormalize($data, Comment::class);
    }

    /**
     * PATCH /comment/{id}
     *
     * @throws HyvorApiException
     */
    public function update(int $id, UpdateCommentRequest $request, ?RequestOptions $options = null): Comment
    {
        $body = $this->transport->normalize($request);
        $data = $this->request('PATCH', $this->path("/comment/{$id}"), $body, $options);

        return $this->transport->denormalize($data, Comment::class);
    }

    /**
     * DELETE /comment/{id}
     *
     * @throws HyvorApiException
     */
    public function delete(int $id, ?RequestOptions $options = null): void
    {
        $this->request('DELETE', $this->path("/comment/{$id}"), null, $options);
    }

    /**
     * POST /comment/{id}/reply
     *
     * By default, the website owner's account replies. See
     * `TalkClient::website()`'s `$headers` to reply as a different moderator.
     *
     * @throws HyvorApiException
     */
    public function reply(int $id, ReplyToCommentRequest $request, ?RequestOptions $options = null): Comment
    {
        $body = $this->transport->normalize($request);
        $data = $this->request('POST', $this->path("/comment/{$id}/reply"), $body, $options);

        return $this->transport->denormalize($data, Comment::class);
    }

    /**
     * POST /comment/{id}/vote
     *
     * `$request->type` is sent verbatim (including null, to remove a vote) —
     * see `VoteOnCommentRequest`.
     *
     * @throws HyvorApiException
     */
    public function vote(int $id, VoteOnCommentRequest $request, ?RequestOptions $options = null): Comment
    {
        $body = [
            'type' => $request->type?->value,
            'user_sso_id' => $request->user_sso_id,
        ];
        $data = $this->request('POST', $this->path("/comment/{$id}/vote"), $body, $options);

        return $this->transport->denormalize($data, Comment::class);
    }

    /**
     * GET /comment/{id}/voters
     *
     * @return LoggedInUser[]
     * @throws HyvorApiException
     */
    public function voters(int $id, GetVotersRequest $request, ?RequestOptions $options = null): array
    {
        $body = $this->transport->normalize($request);
        $data = $this->request('GET', $this->path("/comment/{$id}/voters"), $body, $options);

        return $this->transport->denormalizeList($data, LoggedInUser::class);
    }

    /**
     * DELETE /comment/{id}/vote
     *
     * @param string $userHtid ID of the user whose vote to delete, e.g. `hyvor_100` | `sso_100`.
     * @throws HyvorApiException
     */
    public function deleteVote(int $id, string $userHtid, ?RequestOptions $options = null): void
    {
        $this->request('DELETE', $this->path("/comment/{$id}/vote"), ['user_htid' => $userHtid], $options);
    }

    /**
     * GET /comment/{id}/flags
     *
     * @return Flag[]
     * @throws HyvorApiException
     */
    public function flags(int $id, ?GetFlagsRequest $request = null, ?RequestOptions $options = null): array
    {
        $body = $this->transport->normalize($request ?? new GetFlagsRequest());
        $data = $this->request('GET', $this->path("/comment/{$id}/flags"), $body, $options);

        return $this->transport->denormalizeList($data, Flag::class);
    }

    /**
     * POST /comment/{id}/flag
     *
     * @throws HyvorApiException
     */
    public function createFlag(int $id, ?CreateFlagRequest $request = null, ?RequestOptions $options = null): Comment
    {
        $body = $this->transport->normalize($request ?? new CreateFlagRequest());
        $data = $this->request('POST', $this->path("/comment/{$id}/flag"), $body, $options);

        return $this->transport->denormalize($data, Comment::class);
    }

    /**
     * DELETE /comment/{id}/flag
     *
     * @throws HyvorApiException
     */
    public function deleteFlag(int $id, int $flagId, ?RequestOptions $options = null): void
    {
        $this->request('DELETE', $this->path("/comment/{$id}/flag"), ['flag_id' => $flagId], $options);
    }

    /**
     * POST /comments/bulk-moderate
     *
     * @throws HyvorApiException
     */
    public function bulkModerate(BulkModerateRequest $request, ?RequestOptions $options = null): void
    {
        $body = $this->transport->normalize($request);
        $this->request('POST', $this->path('/comments/bulk-moderate'), $body, $options);
    }
}
