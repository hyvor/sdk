<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests;

use Hyvor\Sdk\Auth\StaticTokenProvider;
use Hyvor\Sdk\HyvorClient;
use Hyvor\Sdk\Tests\Support\FakeHttpClient;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;

final class HyvorClientTest extends TestCase
{
    public function testAllowsNoCredentialsProvided(): void
    {
        // Valid: resource-level API keys can be passed per-call instead
        // (e.g. $client->talk->website($id, 'resource-api-key')).
        $factory = new Psr17Factory();

        $client = new HyvorClient(httpClient: new FakeHttpClient(), requestFactory: $factory, streamFactory: $factory);
        self::assertInstanceOf(HyvorClient::class, $client);
    }

    public function testThrowsWhenBothCredentialsProvided(): void
    {
        $factory = new Psr17Factory();

        $this->expectException(\InvalidArgumentException::class);
        new HyvorClient(
            httpClient: new FakeHttpClient(),
            requestFactory: $factory,
            streamFactory: $factory,
            cloudApiKey: 'abc',
            tokenProvider: new StaticTokenProvider('abc'),
        );
    }

    public function testAcceptsTokenProvider(): void
    {
        $factory = new Psr17Factory();

        $client = new HyvorClient(
            httpClient: new FakeHttpClient(),
            requestFactory: $factory,
            streamFactory: $factory,
            tokenProvider: new StaticTokenProvider('abc'),
        );
        self::assertInstanceOf(HyvorClient::class, $client);
    }

    public function testAcceptsCloudApiKey(): void
    {
        $factory = new Psr17Factory();

        $client = new HyvorClient(
            httpClient: new FakeHttpClient(),
            requestFactory: $factory,
            streamFactory: $factory,
            cloudApiKey: 'abc',
        );
        self::assertInstanceOf(HyvorClient::class, $client);
    }

    public function testDiscoversHttpClientAndFactoriesWhenNotProvided(): void
    {
        // No httpClient/requestFactory/streamFactory given: php-http/discovery
        // must find guzzlehttp/guzzle + guzzlehttp/psr7 (installed for tests).
        $client = new HyvorClient(cloudApiKey: 'abc');
        self::assertInstanceOf(HyvorClient::class, $client);
    }
}
