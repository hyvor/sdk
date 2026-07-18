<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Support;

use Hyvor\Sdk\Auth\StaticTokenProvider;
use Hyvor\Sdk\HyvorClient;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * Shared helpers for Post resource tests: a preconfigured client and sample
 * JSON payloads for objects that are nested inside many endpoint responses
 * (Subscriber, UserMini, ...), so each test file doesn't have to redefine
 * them.
 */
abstract class PostTestCase extends TestCase
{
    protected const NEWSLETTER_ID = 'nl_42';

    protected function client(FakeHttpClient $httpClient, int $retryMaxAttempts = 3): HyvorClient
    {
        $factory = new Psr17Factory();

        return new HyvorClient(
            httpClient: $httpClient,
            requestFactory: $factory,
            streamFactory: $factory,
            tokenProvider: new StaticTokenProvider('test-jwt-token'),
            retryMaxAttempts: $retryMaxAttempts,
        );
    }

    protected function baseUrl(): string
    {
        return 'https://post.hyvor.com/api/console';
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
    protected function sampleSubscriber(array $overrides = []): array
    {
        return array_merge([
            'id' => 1,
            'email' => 'jane@example.com',
            'source' => 'console',
            'status' => 'subscribed',
            'list_ids' => [1],
            'lists' => ['Default'],
            'subscribe_ip' => null,
            'is_opted_in' => true,
            'subscribed_at' => 1700000000,
            'metadata' => [],
        ], $overrides);
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    protected function sampleUserMini(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Bob',
            'email' => 'bob@example.com',
            'username' => 'bob',
            'picture_url' => null,
        ], $overrides);
    }
}
