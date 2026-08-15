<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Newsletter;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Post\Dto\User;
use Hyvor\Sdk\Post\Newsletter;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->newsletter($newsletterId)->users`
 */
final class UsersResource
{
    public function __construct(private readonly Newsletter $client)
    {
    }

    /**
     * GET /api/console/users
     *
     * @return User[]
     *
     * @throws HyvorApiException
     */
    public function list(?RequestOptions $options = null): array
    {
        $result = $this->client->request('GET', $this->client->path('/api/console/users'), null, $options);

        return $this->client->transport->denormalizeList($result, User::class);
    }

    /**
     * POST /api/console/users
     *
     * @param array{
     *     user_id: int,
     *     on_duplicate?: 'throw'|'ignore',
     * } $data
     *
     * @throws HyvorApiException
     */
    public function create(array $data, ?RequestOptions $options = null): User
    {
        $result = $this->client->request('POST', $this->client->path('/api/console/users'), $data, $options);

        return $this->client->transport->denormalize($result, User::class);
    }

    /**
     * DELETE /api/console/users
     *
     * @param array{
     *     user_id?: int|null,
     *     id?: int|null,
     * } $data
     *
     * @throws HyvorApiException
     */
    public function delete(array $data, ?RequestOptions $options = null): void
    {
        $this->client->request('DELETE', $this->client->path('/api/console/users'), $data, $options);
    }
}
