<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Relay;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Http\Transport;
use Hyvor\Sdk\Relay\Dto\Project as DtoProject;
use Hyvor\Sdk\Relay\Project\AnalyticsResource;
use Hyvor\Sdk\Relay\Project\ApiKeysResource;
use Hyvor\Sdk\Relay\Project\DomainsResource;
use Hyvor\Sdk\Relay\Project\ProjectUsersResource;
use Hyvor\Sdk\Relay\Project\SendsResource;
use Hyvor\Sdk\Relay\Project\SuppressionsResource;
use Hyvor\Sdk\Relay\Project\WebhooksResource;
use Hyvor\Sdk\RequestOptions;

/**
 * Resource-level access to a single project, accessible via
 * `$client->project($projectId)`.
 *
 * Authenticated either with the client's org-level auth (a cloud API
 * key or token provider, which must have access to this project), or
 * with a resource-level API key, passed as `$apiKey`.
 */
final class Project
{
    public readonly AnalyticsResource $analytics;
    public readonly ApiKeysResource $api_keys;
    public readonly DomainsResource $domains;
    public readonly ProjectUsersResource $project_users;
    public readonly SendsResource $sends;
    public readonly SuppressionsResource $suppressions;
    public readonly WebhooksResource $webhooks;
    /** @var array<string, string> */
    private readonly array $resourceHeaders;

    /**
     * @param array<string, string> $headers Default headers merged into
     *  every request made through this client and its sub-resources.
     */
    public function __construct(
        public readonly Transport $transport,
        private readonly int|string $projectId,
        private readonly ?string $apiKey = null,
        private readonly array $headers = [],
    ) {
        $this->resourceHeaders = ['X-Project-Id' => (string) $projectId, ...$headers];

        $this->analytics = new AnalyticsResource($this);
        $this->api_keys = new ApiKeysResource($this);
        $this->domains = new DomainsResource($this);
        $this->project_users = new ProjectUsersResource($this);
        $this->sends = new SendsResource($this);
        $this->suppressions = new SuppressionsResource($this);
        $this->webhooks = new WebhooksResource($this);
    }

    public function path(string $suffix = ''): string
    {
        return $suffix;
    }

    /**
     * @param array<mixed>|null $jsonBody
     * @return array<mixed>
     *
     * @throws HyvorApiException
     */
    public function request(
        string $method,
        string $path,
        ?array $jsonBody = null,
        ?RequestOptions $options = null,
    ): array {
        return $this->transport->request($method, $path, $jsonBody, $options, $this->apiKey, $this->resourceHeaders);
    }

    /**
     * GET /api/console/project
     *
     * @throws HyvorApiException
     */
    public function get(?RequestOptions $options = null): DtoProject
    {
        $result = $this->request('GET', $this->path('/api/console/project'), null, $options);

        return $this->transport->denormalize($result, DtoProject::class);
    }

    /**
     * PATCH /api/console/project
     *
     * @param array{
     *     name: string,
     * } $data
     *
     * @throws HyvorApiException
     */
    public function update(array $data, ?RequestOptions $options = null): DtoProject
    {
        $result = $this->request('PATCH', $this->path('/api/console/project'), $data, $options);

        return $this->transport->denormalize($result, DtoProject::class);
    }
}
