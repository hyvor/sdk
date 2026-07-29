<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Post\Dto\User\User;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->post->newsletter($newsletterId)->users`
 */
final class UsersResource extends NewsletterScopedResource
{
    /**
     * GET /users
     *
     * @return User[]
     * @throws HyvorApiException
     */
    public function list(?RequestOptions $options = null): array
    {
        $data = $this->request('GET', $this->path('/users'), null, $options);

        return $this->transport->denormalizeList($data, User::class);
    }

    /**
     * POST /users
     *
     * @param array{
     *     user_id: int,
     *     on_duplicate?: 'throw'|'ignore',
     * } $data user_id: the user's id in HYVOR. on_duplicate:
     *  what to do if the user is already added to the newsletter; throw
     *  returns a 400 error (default), ignore returns the existing user
     *  without an error.
     * @throws HyvorApiException
     */
    public function create(array $data, ?RequestOptions $options = null): User
    {
        $result = $this->request('POST', $this->path('/users'), $data, $options);

        return $this->transport->denormalize($result, User::class);
    }

    /**
     * DELETE /users
     *
     * @param array{
     *     user_id?: int,
     *     id?: int,
     * } $data one of user_id or id is required. user_id: the user's id in
     *  HYVOR. id: the user's id in this newsletter's user
     *  list.
     * @throws HyvorApiException
     */
    public function delete(array $data, ?RequestOptions $options = null): void
    {
        $this->request('DELETE', $this->path('/users'), $data, $options);
    }
}
