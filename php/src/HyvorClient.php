<?php

declare(strict_types=1);

namespace Hyvor\Sdk;

use Hyvor\Sdk\Auth\CloudApiKeyTokenProvider;
use Hyvor\Sdk\Auth\TokenProviderInterface;
use Hyvor\Sdk\Http\Transport;
use Hyvor\Sdk\Talk\TalkClient;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * The main entry point to the Hyvor SDK.
 *
 * ```php
 * $client = new HyvorClient(
 *     httpClient: $psr18Client,
 *     requestFactory: $psr17Factory,
 *     streamFactory: $psr17Factory,
 *     cloudApiKey: '...',
 * );
 * $website = $client->talk->website->get();
 * ```
 */
final class HyvorClient
{
    public readonly TalkClient $talk;

    public function __construct(
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory,
        ?string $cloudApiKey = null,
        ?TokenProviderInterface $tokenProvider = null,
        string $cloudInstance = 'https://hyvor.com',
        ?LoggerInterface $logger = null,
        int $retryMaxAttempts = 3,
        float $retryBackoffFactor = 2.0,
    ) {
        if ($cloudApiKey === null && $tokenProvider === null) {
            throw new \InvalidArgumentException('Either cloudApiKey or tokenProvider must be provided.');
        }

        if ($cloudApiKey !== null && $tokenProvider !== null) {
            throw new \InvalidArgumentException('Provide either cloudApiKey or tokenProvider, not both.');
        }

        $logger ??= new NullLogger();
        $baseUrl = rtrim($cloudInstance, '/');

        if ($tokenProvider === null) {
            /** @var string $cloudApiKey */
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
