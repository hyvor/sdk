<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Relay;

use Hyvor\Sdk\Auth\TokenProviderInterface;
use Hyvor\Sdk\HyvorBaseClientAbstract;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;

/**
 * The entry point to the Hyvor Relay SDK.
 *
 * ```php
 * // org-level access, via a cloud API key
 * $client = new RelayClient(cloudApiKey: '...');
 * $client->org->projects->create(...);
 *
 * // resource-level access, via a per-product API key, no client-level auth needed
 * $client = new RelayClient();
 * $project = $client->project($projectId, 'your-product-api-key');
 *
 * // self-hosted: point directly at your own instance instead of *.hyvor.com
 * $client = new RelayClient(tokenProvider: $yourTokenProvider, productUrl: 'https://relay.example.com');
 * ```
 *
 * See {@see HyvorBaseClientAbstract} for the full constructor parameter docs.
 */
final class RelayClient extends HyvorBaseClientAbstract
{
    /**
     * Org-level access to Relay resources, accessible via `$client->org`.
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
        return 'relay';
    }

    /**
     * Resource-level access to a single project.
     *
     * @param int|string $projectId The project's ID.
     * @param string|null $apiKey A resource-level API key scoped to this
     *  project. If omitted, the client's org-level auth is used instead.
     * @param array<string, string> $headers Default headers merged into
     *  every request made through the returned client (and its
     *  sub-resources). Can be overridden per-call via
     *  `RequestOptions::$headers`.
     */
    public function project(int|string $projectId, ?string $apiKey = null, array $headers = []): Project
    {
        return new Project($this->transport, $projectId, $apiKey, $headers);
    }
}
