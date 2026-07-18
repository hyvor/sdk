<?php

declare(strict_types=1);

namespace Hyvor\Sdk;

use Hyvor\Sdk\Auth\CloudApiKeyTokenProvider;
use Hyvor\Sdk\Auth\TokenProviderInterface;
use Hyvor\Sdk\Http\Transport;
use Hyvor\Sdk\Talk\TalkClient;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * The main entry point to the Hyvor SDK.
 *
 * ```php
 * // org-level access, via a cloud API key
 * $client = new HyvorClient(cloudApiKey: '...');
 * $website = $client->talk->websites->create(new CreateWebsiteRequest(...));
 *
 * // resource-level access, via a per-product API key, no client-level auth needed
 * $client = new HyvorClient();
 * $website = $client->talk->website($websiteId, 'your-product-api-key')->get();
 * ```
 *
 * If `httpClient`/`requestFactory`/`streamFactory` are not given, they are
 * auto-discovered via php-http/discovery from whatever PSR-18/17
 * implementation is installed (e.g. guzzlehttp/guzzle, nyholm/psr7).
 */
final class HyvorClient
{
    public readonly TalkClient $talk;

    public function __construct(
        ?string $cloudApiKey = null,
        ?TokenProviderInterface $tokenProvider = null,
        string $cloudInstance = 'https://hyvor.com',
        ?LoggerInterface $logger = null,
        ?ClientInterface $httpClient = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?StreamFactoryInterface $streamFactory = null,
        int $retryMaxAttempts = 3,
        float $retryBackoffFactor = 2.0,
    ) {
        if ($cloudApiKey !== null && $tokenProvider !== null) {
            throw new \InvalidArgumentException('Provide either cloudApiKey or tokenProvider, not both.');
        }

        $logger ??= new NullLogger();
        $httpClient ??= Psr18ClientDiscovery::find();
        $requestFactory ??= Psr17FactoryDiscovery::findRequestFactory();
        $streamFactory ??= Psr17FactoryDiscovery::findStreamFactory();
        $baseUrl = rtrim($cloudInstance, '/');

        if ($tokenProvider === null && $cloudApiKey !== null) {
            $tokenProvider = new CloudApiKeyTokenProvider(
                $cloudApiKey,
                $baseUrl,
                $httpClient,
                $requestFactory,
                $streamFactory,
                $logger,
            );
        }

        $transport = new Transport(
            httpClient: $httpClient,
            requestFactory: $requestFactory,
            streamFactory: $streamFactory,
            logger: $logger,
            tokenProvider: $tokenProvider,
            baseUrl: $baseUrl,
            defaultRetryMaxAttempts: $retryMaxAttempts,
            defaultRetryBackoffFactor: $retryBackoffFactor,
            userAgent: 'hyvor/sdk-php/' . Version::VERSION,
        );

        $this->talk = new TalkClient($transport);
    }
}
