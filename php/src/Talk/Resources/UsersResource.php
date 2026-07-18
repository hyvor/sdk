<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\RequestOptions;
use Hyvor\Sdk\Talk\Dto\User\EmailNotificationStatus;
use Hyvor\Sdk\Talk\Dto\User\ListUsersRequest;
use Hyvor\Sdk\Talk\Dto\User\LoggedInUser;
use Hyvor\Sdk\Talk\Dto\User\UpdateEmailNotificationRequest;
use Hyvor\Sdk\Talk\Dto\User\UpdateUserRequest;
use Hyvor\Sdk\Talk\Dto\User\UserCounts;

/**
 * `$client->talk->website($websiteId)->users`
 */
final class UsersResource extends WebsiteScopedResource
{
    /**
     * GET /users
     *
     * @return LoggedInUser[]
     * @throws HyvorApiException
     */
    public function list(?ListUsersRequest $request = null, ?RequestOptions $options = null): array
    {
        $body = $this->transport->normalize($request ?? new ListUsersRequest());
        $data = $this->request('GET', $this->path('/users'), $body, $options);

        return $this->transport->denormalizeList($data, LoggedInUser::class);
    }

    /**
     * GET /user/{id}
     *
     * @param int|string $id By default the user's `htid`. Pass
     *  `$bySsoId: true` to instead resolve it as the SSO user ID in your
     *  system (sets the X-ID-Type header).
     * @throws HyvorApiException
     */
    public function get(int|string $id, bool $bySsoId = false, ?RequestOptions $options = null): LoggedInUser
    {
        $data = $this->request('GET', $this->path("/user/{$id}"), null, $options, $this->idTypeHeader($bySsoId));

        return $this->transport->denormalize($data, LoggedInUser::class);
    }

    /**
     * PATCH /user/{id}
     *
     * @throws HyvorApiException
     */
    public function update(
        int|string $id,
        UpdateUserRequest $request,
        bool $bySsoId = false,
        ?RequestOptions $options = null,
    ): LoggedInUser {
        $body = $this->transport->normalize($request);
        $data = $this->request('PATCH', $this->path("/user/{$id}"), $body, $options, $this->idTypeHeader($bySsoId));

        return $this->transport->denormalize($data, LoggedInUser::class);
    }

    /**
     * GET /user/{id}/counts
     *
     * @throws HyvorApiException
     */
    public function counts(int|string $id, bool $bySsoId = false, ?RequestOptions $options = null): UserCounts
    {
        $data = $this->request('GET', $this->path("/user/{$id}/counts"), null, $options, $this->idTypeHeader($bySsoId));

        return $this->transport->denormalize($data, UserCounts::class);
    }

    /**
     * DELETE /user/{id}
     *
     * @param bool $data Set true to delete the user's comments/reactions/etc.
     *  along with their profile. By default, only the profile is deleted and
     *  their comments are shown as "Anonymous user".
     * @throws HyvorApiException
     */
    public function delete(
        int|string $id,
        bool $data = false,
        bool $bySsoId = false,
        ?RequestOptions $options = null,
    ): void {
        $this->request('DELETE', $this->path("/user/{$id}"), ['data' => $data], $options, $this->idTypeHeader($bySsoId));
    }

    /**
     * GET /user/{id}/email-notification
     *
     * @throws HyvorApiException
     */
    public function getEmailNotification(
        int|string $id,
        bool $bySsoId = false,
        ?RequestOptions $options = null,
    ): EmailNotificationStatus {
        $data = $this->request(
            'GET',
            $this->path("/user/{$id}/email-notification"),
            null,
            $options,
            $this->idTypeHeader($bySsoId),
        );

        return $this->transport->denormalize($data, EmailNotificationStatus::class);
    }

    /**
     * POST /user/{id}/email-notification
     *
     * @throws HyvorApiException
     */
    public function updateEmailNotification(
        int|string $id,
        UpdateEmailNotificationRequest $request,
        bool $bySsoId = false,
        ?RequestOptions $options = null,
    ): void {
        $body = $this->transport->normalize($request);
        $this->request(
            'POST',
            $this->path("/user/{$id}/email-notification"),
            $body,
            $options,
            $this->idTypeHeader($bySsoId),
        );
    }

    /**
     * @return array<string, string>
     */
    private function idTypeHeader(bool $bySsoId): array
    {
        return $bySsoId ? ['X-ID-Type' => 'sso_user_id'] : [];
    }
}
