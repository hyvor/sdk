<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Support;

use Hyvor\Sdk\Auth\StaticTokenProvider;
use Hyvor\Sdk\Talk\TalkClient;
use Hyvor\Sdk\Testing\FakeHttpClient;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * Shared helpers for Talk resource tests: a preconfigured client and sample
 * JSON payloads for objects that are nested inside many endpoint responses
 * (AuthUser, Domain, ...), so each test file doesn't have to redefine them.
 */
abstract class TalkTestCase extends TestCase
{
    protected const WEBSITE_ID = 42;

    protected function client(FakeHttpClient $httpClient, int $retryMaxAttempts = 3): TalkClient
    {
        $factory = new Psr17Factory();

        return new TalkClient(
            httpClient: $httpClient,
            requestFactory: $factory,
            streamFactory: $factory,
            tokenProvider: new StaticTokenProvider('test-jwt-token'),
            retryMaxAttempts: $retryMaxAttempts,
        );
    }

    protected function baseUrl(): string
    {
        return 'https://talk.hyvor.com/api/console/v1/' . self::WEBSITE_ID;
    }

    /**
     * @param array<mixed> $data
     */
    protected function queueJson(FakeHttpClient $http, array $data, int $status = 200): void
    {
        $http->queueResponse(new Response($status, [], json_encode($data, JSON_THROW_ON_ERROR)));
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    protected function sampleAuthUser(array $overrides = []): array
    {
        return array_merge([
            'id' => 1,
            'username' => 'bob',
            'name' => 'Bob',
            'email' => 'bob@example.com',
            'picture_url' => null,
            'location' => null,
            'bio' => null,
            'website_url' => null,
            'oidc_sub' => null,
        ], $overrides);
    }
}
