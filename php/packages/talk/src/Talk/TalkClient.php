<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk;

use Hyvor\Sdk\Auth\TokenProviderInterface;
use Hyvor\Sdk\Http\Transport;
use Hyvor\Sdk\Http\TransportBuilder;
use Hyvor\Sdk\Talk\Org\OrgWebsitesResource;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;

/**
 * The entry point to the Hyvor Talk SDK.
 *
 * ```php
 * // org-level access, via a cloud API key
 * $talk = new TalkClient(cloudApiKey: '...');
 * $website = $talk->websites->create(new CreateWebsiteRequest(...));
 *
 * // resource-level access, via a per-product API key, no client-level auth needed
 * $talk = new TalkClient();
 * $website = $talk->website($websiteId, 'your-product-api-key')->get();
 *
 * // self-hosted: point directly at your own instance instead of *.hyvor.com
 * $talk = new TalkClient(tokenProvider: $yourTokenProvider, productUrl: 'https://talk.example.com');
 * ```
 *
 * If `httpClient`/`requestFactory`/`streamFactory` are not given, they are
 * auto-discovered via php-http/discovery from whatever PSR-18/17
 * implementation is installed (e.g. guzzlehttp/guzzle, nyholm/psr7).
 */
final class TalkClient
{
    /**
     * Org-level access to all websites, accessible via `$talk->websites`.
     */
    public readonly OrgWebsitesResource $websites;

    private readonly Transport $transport;

    public function __construct(
        ?string $cloudApiKey = null,
        ?TokenProviderInterface $tokenProvider = null,
        /**
         * Overrides the product URL derived from `cloudInstance` - set this
         * for a self-hosted instance (e.g. `https://talk.example.com`).
         */
        ?string $productUrl = null,
        ?LoggerInterface $logger = null,
        ?ClientInterface $httpClient = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?StreamFactoryInterface $streamFactory = null,
        int $retryMaxAttempts = 3,
        float $retryBackoffFactor = 2.0,
        /**
         * Only relevant for hyvor.com-hosted (cloud) usage - self-hosted
         * users should set `productUrl` instead.
         */
        string $cloudInstance = 'https://hyvor.com',
    ) {
        $this->transport = TransportBuilder::build(
            product: 'talk',
            cloudApiKey: $cloudApiKey,
            tokenProvider: $tokenProvider,
            productUrl: $productUrl,
            logger: $logger,
            httpClient: $httpClient,
            requestFactory: $requestFactory,
            streamFactory: $streamFactory,
            retryMaxAttempts: $retryMaxAttempts,
            retryBackoffFactor: $retryBackoffFactor,
            cloudInstance: $cloudInstance,
        );
        $this->websites = new OrgWebsitesResource($this->transport);
    }

    /**
     * Resource-level access to a single website.
     *
     * @param int|string $websiteId The website's ID.
     * @param string|null $apiKey A resource-level API key scoped to this
     *  website. If omitted, the client's org-level auth is used instead.
     * @param array<string, string> $headers Default headers merged into
     *  every request made through the returned client (and its
     *  sub-resources). Can be overridden per-call via
     *  `RequestOptions::$headers`.
     */
    public function website(int|string $websiteId, ?string $apiKey = null, array $headers = []): WebsiteClient
    {
        return new WebsiteClient($this->transport, $websiteId, $apiKey, $headers);
    }
}
