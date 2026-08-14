<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Http\Transport;
use Hyvor\Sdk\Post\Dto\Newsletter as DtoNewsletter;
use Hyvor\Sdk\Post\Newsletter\ApiKeysResource;
use Hyvor\Sdk\Post\Newsletter\ExportsResource;
use Hyvor\Sdk\Post\Newsletter\ImportsResource;
use Hyvor\Sdk\Post\Newsletter\IssuesResource;
use Hyvor\Sdk\Post\Newsletter\ListsResource;
use Hyvor\Sdk\Post\Newsletter\MediaResource;
use Hyvor\Sdk\Post\Newsletter\SendingProfilesResource;
use Hyvor\Sdk\Post\Newsletter\SubscriberMetadataResource;
use Hyvor\Sdk\Post\Newsletter\SubscribersResource;
use Hyvor\Sdk\Post\Newsletter\TemplatesResource;
use Hyvor\Sdk\Post\Newsletter\UsersResource;
use Hyvor\Sdk\RequestOptions;

/**
 * Resource-level access to a single newsletter, accessible via
 * `$client->newsletter($newsletterId)`.
 *
 * Authenticated either with the client's org-level auth (a cloud API
 * key or token provider, which must have access to this newsletter), or
 * with a resource-level API key, passed as `$apiKey`.
 */
final class Newsletter
{
    public readonly ApiKeysResource $api_keys;
    public readonly ExportsResource $exports;
    public readonly ImportsResource $imports;
    public readonly IssuesResource $issues;
    public readonly ListsResource $lists;
    public readonly MediaResource $media;
    public readonly SendingProfilesResource $sending_profiles;
    public readonly SubscriberMetadataResource $subscriber_metadata;
    public readonly SubscribersResource $subscribers;
    public readonly TemplatesResource $templates;
    public readonly UsersResource $users;
    /** @var array<string, string> */
    private readonly array $resourceHeaders;

    /**
     * @param array<string, string> $headers Default headers merged into
     *  every request made through this client and its sub-resources.
     */
    public function __construct(
        public readonly Transport $transport,
        private readonly int|string $newsletterId,
        private readonly ?string $apiKey = null,
        private readonly array $headers = [],
    ) {
        $this->resourceHeaders = ['X-Newsletter-Id' => (string) $newsletterId, ...$headers];

        $this->api_keys = new ApiKeysResource($this);
        $this->exports = new ExportsResource($this);
        $this->imports = new ImportsResource($this);
        $this->issues = new IssuesResource($this);
        $this->lists = new ListsResource($this);
        $this->media = new MediaResource($this);
        $this->sending_profiles = new SendingProfilesResource($this);
        $this->subscriber_metadata = new SubscriberMetadataResource($this);
        $this->subscribers = new SubscribersResource($this);
        $this->templates = new TemplatesResource($this);
        $this->users = new UsersResource($this);
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
     * GET /api/console/newsletter
     *
     * @throws HyvorApiException
     */
    public function get(?RequestOptions $options = null): DtoNewsletter
    {
        $result = $this->request('GET', $this->path('/api/console/newsletter'), null, $options);

        return $this->transport->denormalize($result, DtoNewsletter::class);
    }

    /**
     * DELETE /api/console/newsletter
     *
     * @throws HyvorApiException
     */
    public function delete(?RequestOptions $options = null): void
    {
        $this->request('DELETE', $this->path('/api/console/newsletter'), null, $options);
    }

    /**
     * PATCH /api/console/newsletter
     *
     * @param array{
     *     name: string,
     *     subdomain: string,
     * } $data
     *
     * @throws HyvorApiException
     */
    public function update(array $data, ?RequestOptions $options = null): DtoNewsletter
    {
        $result = $this->request('PATCH', $this->path('/api/console/newsletter'), $data, $options);

        return $this->transport->denormalize($result, DtoNewsletter::class);
    }
}
