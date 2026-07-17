<?php

declare(strict_types=1);

namespace Hyvor\Sdk;

use Hyvor\Sdk\Auth\TokenProvider;
use Hyvor\Sdk\Http\CurlHttpClient;
use Hyvor\Sdk\Http\HttpClientInterface;
use Hyvor\Sdk\Http\Transport;
use Hyvor\Sdk\Talk\TalkClient;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * The main entry point to the Hyvor SDK.
 *
 * ```php
 * $client = new HyvorClient(cloudApiKey: '...');
 * $website = $client->talk->website->get();
 * ```
 */
final class HyvorClient
{
    public readonly TalkClient $talk;

    public function __construct(
        ?string $cloudApiKey = null,
        ?string $jwtToken = null,
        string $cloudInstance = 'https://hyvor.com',
        ?LoggerInterface $logger = null,
        ?HttpClientInterface $httpClient = null,
        int $connectTimeoutMs = 5000,
        int $requestTimeoutMs = 10000,
        int $retryMaxAttempts = 3,
        float $retryBackoffFactor = 2.0,
    ) {
        if ($cloudApiKey === null && $jwtToken === null) {
            throw new \InvalidArgumentException('Either cloudApiKey or jwtToken must be provided.');
        }

        $logger ??= new NullLogger();
        $httpClient ??= new CurlHttpClient();
        $baseUrl = rtrim($cloudInstance, '/');

        $tokenProvider = new TokenProvider($cloudApiKey, $jwtToken, $baseUrl, $httpClient, $logger);

        $transport = new Transport(
            httpClient: $httpClient,
            logger: $logger,
            tokenProvider: $tokenProvider,
            baseUrl: $baseUrl,
            defaultConnectTimeoutMs: $connectTimeoutMs,
            defaultRequestTimeoutMs: $requestTimeoutMs,
            defaultRetryMaxAttempts: $retryMaxAttempts,
            defaultRetryBackoffFactor: $retryBackoffFactor,
            userAgent: 'hyvor/sdk-php/' . Version::VERSION,
        );

        $this->talk = new TalkClient($transport);
    }
}
