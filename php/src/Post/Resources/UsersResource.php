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
     * DELETE /users/{id}
     *
     * @throws HyvorApiException
     */
    public function delete(int $id, ?RequestOptions $options = null): void
    {
        $this->request('DELETE', $this->path("/users/{$id}"), null, $options);
    }
}
