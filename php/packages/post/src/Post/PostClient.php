<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post;

use Hyvor\Sdk\Auth\TokenProviderInterface;
use Hyvor\Sdk\HyvorBaseClientAbstract;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;

/**
 * The entry point to the Hyvor Post SDK.
 *
 * ```php
 * // org-level access, via a cloud API key
 * $client = new PostClient(cloudApiKey: '...');
 * $client->org->domain->create(...);
 *
 * // resource-level access, via a per-product API key, no client-level auth needed
 * $client = new PostClient();
 * $newsletter = $client->newsletter($newsletterId, 'your-product-api-key');
 *
 * // self-hosted: point directly at your own instance instead of *.hyvor.com
 * $client = new PostClient(tokenProvider: $yourTokenProvider, productUrl: 'https://post.example.com');
 * ```
 *
 * See {@see HyvorBaseClientAbstract} for the full constructor parameter docs.
 */
final class PostClient extends HyvorBaseClientAbstract
{
    /**
     * Org-level access to Post resources, accessible via `$client->org`.
     */
    public readonly Org $org;

    public function __construct(
        ?string $cloudApiKey = null,
        ?TokenProviderInterface $tokenProvider = null,
        ?string $productUrl = null,
        ?LoggerInterface $logger = null,
        ?ClientInterface $httpClient = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?StreamFactoryInterface $streamFactory = null,
        int $retryMaxAttempts = 3,
        float $retryBackoffFactor = 2.0,
        string $cloudInstance = 'https://hyvor.com',
    ) {
        parent::__construct(
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
        $this->org = new Org($this->transport);
    }

    protected function product(): string
    {
        return 'post';
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
    public function newsletter(int|string $newsletterId, ?string $apiKey = null, array $headers = []): Newsletter
    {
        return new Newsletter($this->transport, $newsletterId, $apiKey, $headers);
    }
}
