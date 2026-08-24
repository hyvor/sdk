<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Relay\Project;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Relay\Dto\Suppression;
use Hyvor\Sdk\Relay\Project;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->project($projectId)->suppressions`
 */
final class SuppressionsResource
{
    public function __construct(private readonly Project $client)
    {
    }

    /**
     * GET /api/console/suppressions
     *
     * @return Suppression[]
     *
     * @throws HyvorApiException
     */
    public function list(?RequestOptions $options = null): array
    {
        $result = $this->client->request('GET', $this->client->path('/api/console/suppressions'), null, $options);

        return $this->client->transport->denormalizeList($result, Suppression::class);
    }

    /**
     * DELETE /api/console/suppressions/{id}
     *
     * @throws HyvorApiException
     */
    public function delete(int $id, ?RequestOptions $options = null): void
    {
        $this->client->request('DELETE', $this->client->path('/api/console/suppressions/' . $id), null, $options);
    }
}
