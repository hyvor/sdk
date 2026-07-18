<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Http\Transport;
use Hyvor\Sdk\Post\Dto\Newsletter\Newsletter;
use Hyvor\Sdk\Post\Dto\Newsletter\UpdateNewsletterRequest;
use Hyvor\Sdk\Post\Resources\ExportsResource;
use Hyvor\Sdk\Post\Resources\InvitesResource;
use Hyvor\Sdk\Post\Resources\IssuesResource;
use Hyvor\Sdk\Post\Resources\ListsResource;
use Hyvor\Sdk\Post\Resources\MediaResource;
use Hyvor\Sdk\Post\Resources\SendingProfilesResource;
use Hyvor\Sdk\Post\Resources\SubscriberMetadataDefinitionsResource;
use Hyvor\Sdk\Post\Resources\SubscribersResource;
use Hyvor\Sdk\Post\Resources\TemplatesResource;
use Hyvor\Sdk\Post\Resources\UsersResource;
use Hyvor\Sdk\RequestOptions;

/**
 * Resource-level access to a single newsletter, accessible via
 * `$client->post->newsletter($newsletterId)`.
 *
 * Authenticated either with the client's org-level auth (a cloud API key or
 * token provider, which must have access to this newsletter), or with a
 * resource-level API key scoped to this newsletter, passed as `$apiKey`.
 *
 * Unlike Talk's Console API, Post's Console API doesn't embed the
 * newsletter's ID in the URL. Instead, every request carries an
 * `X-Newsletter-Id` header, which is what lets an org-level cloud API key
 * (otherwise valid for every newsletter the org can access) resolve to this
 * one newsletter.
 */
final class NewsletterClient
{
    public readonly IssuesResource $issues;
    public readonly ListsResource $lists;
    public readonly SubscribersResource $subscribers;
    public readonly SubscriberMetadataDefinitionsResource $subscriberMetadataDefinitions;
    public readonly SendingProfilesResource $sendingProfiles;
    public readonly TemplatesResource $templates;
    public readonly UsersResource $users;
    public readonly InvitesResource $invites;
    public readonly MediaResource $media;
    public readonly ExportsResource $exports;

    /** @var array<string, string> */
    private readonly array $resourceHeaders;

    /**
     * @param array<string, string> $headers Default headers merged into
     *  every request made through this client and its sub-resources.
     */
    public function __construct(
        private readonly Transport $transport,
        private readonly int|string $newsletterId,
        private readonly ?string $apiKey = null,
        private readonly array $headers = [],
    ) {
        // caller-supplied headers can override X-Newsletter-Id if they need to.
        $this->resourceHeaders = ['X-Newsletter-Id' => (string) $newsletterId, ...$headers];

        $this->issues = new IssuesResource($transport, $apiKey, $this->resourceHeaders);
        $this->lists = new ListsResource($transport, $apiKey, $this->resourceHeaders);
        $this->subscribers = new SubscribersResource($transport, $apiKey, $this->resourceHeaders);
        $this->subscriberMetadataDefinitions = new SubscriberMetadataDefinitionsResource($transport, $apiKey, $this->resourceHeaders);
        $this->sendingProfiles = new SendingProfilesResource($transport, $apiKey, $this->resourceHeaders);
        $this->templates = new TemplatesResource($transport, $apiKey, $this->resourceHeaders);
        $this->users = new UsersResource($transport, $apiKey, $this->resourceHeaders);
        $this->invites = new InvitesResource($transport, $apiKey, $this->resourceHeaders);
        $this->media = new MediaResource($transport, $apiKey, $this->resourceHeaders);
        $this->exports = new ExportsResource($transport, $apiKey, $this->resourceHeaders);
    }

    private function path(string $suffix = ''): string
    {
        return "/api/console{$suffix}";
    }

    /**
     * GET /newsletter
     *
     * @throws HyvorApiException
     */
    public function get(?RequestOptions $options = null): Newsletter
    {
        $data = $this->transport->request(
            'GET',
            $this->path('/newsletter'),
            null,
            $options,
            $this->apiKey,
            $this->resourceHeaders,
        );

        return $this->transport->denormalize($data, Newsletter::class);
    }

    /**
     * PATCH /newsletter
     *
     * @throws HyvorApiException
     */
    public function update(UpdateNewsletterRequest $request, ?RequestOptions $options = null): Newsletter
    {
        $body = $this->transport->normalize($request);
        $data = $this->transport->request(
            'PATCH',
            $this->path('/newsletter'),
            $body,
            $options,
            $this->apiKey,
            $this->resourceHeaders,
        );

        return $this->transport->denormalize($data, Newsletter::class);
    }
}
