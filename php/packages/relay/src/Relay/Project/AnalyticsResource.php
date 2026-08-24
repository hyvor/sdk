<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Relay\Project;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Relay\Project;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->project($projectId)->analytics`
 */
final class AnalyticsResource
{
    public function __construct(private readonly Project $client)
    {
    }

    /**
     * GET /api/console/analytics/stats
     *
     * @return array<mixed>
     *
     * @throws HyvorApiException
     */
    public function stats(?RequestOptions $options = null): array
    {
        return $this->client->request('GET', $this->client->path('/api/console/analytics/stats'), null, $options);
    }

    /**
     * GET /api/console/analytics/sends/chart
     *
     * @return array<mixed>
     *
     * @throws HyvorApiException
     */
    public function sendschart(?RequestOptions $options = null): array
    {
        return $this->client->request('GET', $this->client->path('/api/console/analytics/sends/chart'), null, $options);
    }
}
