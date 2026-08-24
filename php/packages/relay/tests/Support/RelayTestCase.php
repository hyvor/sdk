<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Support;

use Hyvor\Sdk\Auth\StaticTokenProvider;
use Hyvor\Sdk\Relay\RelayClient;
use Hyvor\Sdk\Testing\FakeHttpClient;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * Shared helpers for Relay resource tests: a preconfigured client and sample
 * JSON payloads for objects that are nested inside many endpoint responses,
 * so each test file doesn't have to redefine them.
 */
abstract class RelayTestCase extends TestCase
{
    protected const PROJECT_ID = 42;

    protected function client(FakeHttpClient $httpClient, int $retryMaxAttempts = 3): RelayClient
    {
        $factory = new Psr17Factory();

        return new RelayClient(
            httpClient: $httpClient,
            requestFactory: $factory,
            streamFactory: $factory,
            tokenProvider: new StaticTokenProvider('test-jwt-token'),
            retryMaxAttempts: $retryMaxAttempts,
        );
    }

    protected function baseUrl(): string
    {
        return 'https://relay.hyvor.com/api/console';
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
    protected function sampleDomain(array $overrides = []): array
    {
        return array_merge([
            'id' => 1,
            'created_at' => 1700000000,
            'domain' => 'example.com',
            'status' => 'active',
            'status_changed_at' => 1700000000,
            'dkim_selector' => 'default',
            'dkim_host' => 'default._domainkey.example.com',
            'dkim_public_key' => 'publickey',
            'dkim_txt_value' => 'v=DKIM1; k=rsa; p=publickey',
            'dkim_checked_at' => 1700000000,
            'dkim_error_message' => null,
        ], $overrides);
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    protected function sampleProject(array $overrides = []): array
    {
        return array_merge([
            'id' => 1,
            'created_at' => 1700000000,
            'name' => 'My Project',
            'send_type' => 'transactional',
        ], $overrides);
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    protected function sampleSend(array $overrides = []): array
    {
        return array_merge([
            'id' => 1,
            'uuid' => '00000000-0000-4000-8000-000000000000',
            'created_at' => 1700000000,
            'from_address' => 'from@example.com',
            'from_name' => null,
            'subject' => 'Hi',
            'size_bytes' => 1024,
            'queued' => true,
            'send_after' => 1700000000,
            'recipients' => [],
            'attempts' => [],
            'feedback' => [],
        ], $overrides);
    }
}
