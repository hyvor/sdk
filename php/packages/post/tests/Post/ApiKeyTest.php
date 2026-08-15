<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Post;

use Hyvor\Sdk\Testing\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\PostTestCase;

final class ApiKeyTest extends PostTestCase
{
    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function sampleApiKey(array $overrides = []): array
    {
        return array_merge([
            'id' => 1,
            'name' => 'CI key',
            'scopes' => ['issues.read'],
            'key' => null,
            'created_at' => 1700000000,
            'is_enabled' => true,
            'last_accessed_at' => null,
        ], $overrides);
    }

    public function testList(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [$this->sampleApiKey()]);

        $keys = $this->client($http)->newsletter(self::NEWSLETTER_ID)->api_keys->list();

        self::assertCount(1, $keys);
        self::assertSame('CI key', $keys[0]->name);
        self::assertSame($this->baseUrl() . '/api-keys', (string) $http->requests[0]->getUri());
    }

    public function testCreate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleApiKey(['key' => 'secret-key-value']), 201);

        $apiKey = $this->client($http)->newsletter(self::NEWSLETTER_ID)->api_keys->create(
            ['name' => 'CI key', 'scopes' => ['issues.read']],
        );

        self::assertSame('secret-key-value', $apiKey->key);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/api-keys', (string) $request->getUri());
        self::assertSame(
            ['name' => 'CI key', 'scopes' => ['issues.read']],
            json_decode((string) $request->getBody(), true),
        );
    }

    public function testRegenerate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleApiKey(['key' => 'new-secret-value']));

        $apiKey = $this->client($http)->newsletter(self::NEWSLETTER_ID)->api_keys->regenerate(1);

        self::assertSame('new-secret-value', $apiKey->key);
        self::assertSame($this->baseUrl() . '/api-keys/1', (string) $http->requests[0]->getUri());
        self::assertSame('POST', $http->requests[0]->getMethod());
    }

    public function testUpdate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleApiKey(['name' => 'Renamed', 'is_enabled' => false]));

        $apiKey = $this->client($http)->newsletter(self::NEWSLETTER_ID)->api_keys->update(
            1,
            ['name' => 'Renamed', 'is_enabled' => false, 'scopes' => ['issues.read']],
        );

        self::assertSame('Renamed', $apiKey->name);
        self::assertFalse($apiKey->is_enabled);

        $request = $http->requests[0];
        self::assertSame('PATCH', $request->getMethod());
        self::assertSame($this->baseUrl() . '/api-keys/1', (string) $request->getUri());
    }

    public function testDelete(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->newsletter(self::NEWSLETTER_ID)->api_keys->delete(1);

        $request = $http->requests[0];
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame($this->baseUrl() . '/api-keys/1', (string) $request->getUri());
    }
}
