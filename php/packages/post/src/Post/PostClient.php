<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post;

use Hyvor\Sdk\Auth\TokenProviderInterface;
use Hyvor\Sdk\Http\Transport;
use Hyvor\Sdk\Http\TransportBuilder;
use Hyvor\Sdk\Post\Org\OrgNewslettersResource;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;

/**
 * The entry point to the Hyvor Post SDK.
 *
 * ```php
 * // org-level access, via a cloud API key
 * $post = new PostClient(cloudApiKey: '...');
 * $newsletter = $post->newsletters->create(new CreateNewsletterRequest(...));
 *
 * // resource-level access, via a per-product API key, no client-level auth needed
 * $post = new PostClient();
 * $newsletter = $post->newsletter($newsletterId, 'your-product-api-key')->get();
 *
 * // self-hosted: point directly at your own instance instead of *.hyvor.com
 * $post = new PostClient(tokenProvider: $yourTokenProvider, productUrl: 'https://post.example.com');
 * ```
 *
 * If `httpClient`/`requestFactory`/`streamFactory` are not given, they are
 * auto-discovered via php-http/discovery from whatever PSR-18/17
 * implementation is installed (e.g. guzzlehttp/guzzle, nyholm/psr7).
 */
final class PostClient
{
    /**
     * Org-level access to newsletters, accessible via `$post->newsletters`.
     */
    public readonly OrgNewslettersResource $newsletters;

    private readonly Transport $transport;

    public function __construct(
        ?string $cloudApiKey = null,
        ?TokenProviderInterface $tokenProvider = null,
        /**
         * Overrides the product URL derived from `cloudInstance` - set this
         * for a self-hosted instance (e.g. `https://post.example.com`).
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
            product: 'post',
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
        $this->newsletters = new OrgNewslettersResource($this->transport);
    }

    /**
     * Resource-level access to a single newsletter.
     *
     * @param int|string $newsletterId The newsletter's ID.
     * @param string|null $apiKey A resource-level API key scoped to this
     *  newsletter. If omitted, the client's org-level auth is used instead.
     * @param array<string, string> $headers Default headers merged into
     *  every request made through the returned client (and its
     *  sub-resources). Can be overridden per-call via
     *  `RequestOptions::$headers`.
     */
    public function newsletter(int|string $newsletterId, ?string $apiKey = null, array $headers = []): NewsletterClient
    {
        return new NewsletterClient($this->transport, $newsletterId, $apiKey, $headers);
    }
}
