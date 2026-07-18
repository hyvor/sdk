<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Http\Transport;
use Hyvor\Sdk\RequestOptions;
use Hyvor\Sdk\Talk\Dto\Website\UpdateWebsiteRequest;
use Hyvor\Sdk\Talk\Dto\Website\Website;
use Hyvor\Sdk\Talk\Resources\AnalyticsResource;
use Hyvor\Sdk\Talk\Resources\BadgesResource;
use Hyvor\Sdk\Talk\Resources\CommentsResource;
use Hyvor\Sdk\Talk\Resources\DomainsResource;
use Hyvor\Sdk\Talk\Resources\EmailDomainResource;
use Hyvor\Sdk\Talk\Resources\EmailLogsResource;
use Hyvor\Sdk\Talk\Resources\IntegrationsResource;
use Hyvor\Sdk\Talk\Resources\IpsResource;
use Hyvor\Sdk\Talk\Resources\JobsResource;
use Hyvor\Sdk\Talk\Resources\MediaResource;
use Hyvor\Sdk\Talk\Resources\ModeratorsResource;
use Hyvor\Sdk\Talk\Resources\PagesResource;
use Hyvor\Sdk\Talk\Resources\RatingsResource;
use Hyvor\Sdk\Talk\Resources\ReactionsResource;
use Hyvor\Sdk\Talk\Resources\RulesResource;
use Hyvor\Sdk\Talk\Resources\SsoResource;
use Hyvor\Sdk\Talk\Resources\UsersResource;
use Hyvor\Sdk\Talk\Resources\WebhooksResource;

/**
 * Resource-level access to a single website, accessible via
 * `$client->talk->website($websiteId)`.
 *
 * Authenticated either with the client's org-level auth (a cloud API key or
 * token provider, which must have access to this website), or with a
 * resource-level API key scoped to this website, passed as `$apiKey`.
 */
final class WebsiteClient
{
    public readonly CommentsResource $comments;
    public readonly ReactionsResource $reactions;
    public readonly RatingsResource $ratings;
    public readonly PagesResource $pages;
    public readonly UsersResource $users;
    public readonly AnalyticsResource $analytics;
    public readonly ModeratorsResource $moderators;
    public readonly EmailDomainResource $emailDomain;
    public readonly RulesResource $rules;
    public readonly EmailLogsResource $emailLogs;
    public readonly IpsResource $ips;
    public readonly DomainsResource $domains;
    public readonly BadgesResource $badges;
    public readonly SsoResource $sso;
    public readonly JobsResource $jobs;
    public readonly WebhooksResource $webhooks;
    public readonly IntegrationsResource $integrations;
    public readonly MediaResource $media;

    /**
     * @param array<string, string> $headers Default headers merged into
     *  every request made through this client and its sub-resources.
     */
    public function __construct(
        private readonly Transport $transport,
        private readonly int|string $websiteId,
        private readonly ?string $apiKey = null,
        private readonly array $headers = [],
    ) {
        $this->comments = new CommentsResource($transport, $websiteId, $apiKey, $headers);
        $this->reactions = new ReactionsResource($transport, $websiteId, $apiKey, $headers);
        $this->ratings = new RatingsResource($transport, $websiteId, $apiKey, $headers);
        $this->pages = new PagesResource($transport, $websiteId, $apiKey, $headers);
        $this->users = new UsersResource($transport, $websiteId, $apiKey, $headers);
        $this->analytics = new AnalyticsResource($transport, $websiteId, $apiKey, $headers);
        $this->moderators = new ModeratorsResource($transport, $websiteId, $apiKey, $headers);
        $this->emailDomain = new EmailDomainResource($transport, $websiteId, $apiKey, $headers);
        $this->rules = new RulesResource($transport, $websiteId, $apiKey, $headers);
        $this->emailLogs = new EmailLogsResource($transport, $websiteId, $apiKey, $headers);
        $this->ips = new IpsResource($transport, $websiteId, $apiKey, $headers);
        $this->domains = new DomainsResource($transport, $websiteId, $apiKey, $headers);
        $this->badges = new BadgesResource($transport, $websiteId, $apiKey, $headers);
        $this->sso = new SsoResource($transport, $websiteId, $apiKey, $headers);
        $this->jobs = new JobsResource($transport, $websiteId, $apiKey, $headers);
        $this->webhooks = new WebhooksResource($transport, $websiteId, $apiKey, $headers);
        $this->integrations = new IntegrationsResource($transport, $websiteId, $apiKey, $headers);
        $this->media = new MediaResource($transport, $websiteId, $apiKey, $headers);
    }

    private function path(string $suffix = ''): string
    {
        return "/api/console/v1/{$this->websiteId}{$suffix}";
    }

    /**
     * GET /website
     *
     * @throws HyvorApiException
     */
    public function get(?RequestOptions $options = null): Website
    {
        $data = $this->transport->request(
            'GET',
            $this->path('/website'),
            null,
            $options,
            $this->apiKey,
            $this->headers,
        );

        return $this->transport->denormalize($data, Website::class);
    }

    /**
     * PATCH /website
     *
     * @throws HyvorApiException
     */
    public function update(UpdateWebsiteRequest $request, ?RequestOptions $options = null): Website
    {
        $body = $this->transport->normalize($request);
        $data = $this->transport->request(
            'PATCH',
            $this->path('/website'),
            $body,
            $options,
            $this->apiKey,
            $this->headers,
        );

        return $this->transport->denormalize($data, Website::class);
    }
}
