<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Post\Dto\User\CreateInviteRequest;
use Hyvor\Sdk\Post\Dto\User\UserInvite;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->post->newsletter($newsletterId)->invites`
 */
final class InvitesResource extends NewsletterScopedResource
{
    /**
     * GET /invites
     *
     * @return UserInvite[]
     * @throws HyvorApiException
     */
    public function list(?RequestOptions $options = null): array
    {
        $data = $this->request('GET', $this->path('/invites'), null, $options);

        return $this->transport->denormalizeList($data, UserInvite::class);
    }

    /**
     * POST /invites
     *
     * The invitee must already have a HYVOR account.
     *
     * @throws HyvorApiException
     */
    public function create(CreateInviteRequest $request, ?RequestOptions $options = null): UserInvite
    {
        $body = $this->transport->normalize($request);
        $data = $this->request('POST', $this->path('/invites'), $body, $options);

        return $this->transport->denormalize($data, UserInvite::class);
    }

    /**
     * DELETE /invites/{id}
     *
     * @throws HyvorApiException
     */
    public function delete(int $id, ?RequestOptions $options = null): void
    {
        $this->request('DELETE', $this->path("/invites/{$id}"), null, $options);
    }
}
