<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Relay\Project;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Relay\Dto\ProjectUser;
use Hyvor\Sdk\Relay\Project;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->project($projectId)->project_users`
 */
final class ProjectUsersResource
{
    public function __construct(private readonly Project $client)
    {
    }

    /**
     * GET /api/console/project-users
     *
     * @return ProjectUser[]
     *
     * @throws HyvorApiException
     */
    public function list(?RequestOptions $options = null): array
    {
        $result = $this->client->request('GET', $this->client->path('/api/console/project-users'), null, $options);

        return $this->client->transport->denormalizeList($result, ProjectUser::class);
    }

    /**
     * POST /api/console/project-users
     *
     * @param array{
     *     user_id: int,
     *     scopes: list<string>,
     * } $data
     *
     * @throws HyvorApiException
     */
    public function create(array $data, ?RequestOptions $options = null): ProjectUser
    {
        $result = $this->client->request('POST', $this->client->path('/api/console/project-users'), $data, $options);

        return $this->client->transport->denormalize($result, ProjectUser::class);
    }

    /**
     * DELETE /api/console/project-users
     *
     * @throws HyvorApiException
     */
    public function deleteall(?RequestOptions $options = null): void
    {
        $this->client->request('DELETE', $this->client->path('/api/console/project-users'), null, $options);
    }

    /**
     * DELETE /api/console/project-users/{id}
     *
     * @throws HyvorApiException
     */
    public function delete(int $id, ?RequestOptions $options = null): void
    {
        $this->client->request('DELETE', $this->client->path('/api/console/project-users/' . $id), null, $options);
    }
}
