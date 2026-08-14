<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Post;

use Hyvor\Sdk\Testing\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\PostTestCase;

final class DomainTest extends PostTestCase
{
    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function sampleDomain(array $overrides = []): array
    {
        return array_merge([
            'id' => 1,
            'created_at' => 1700000000,
            'domain' => 'mail.example.com',
            'dkim_public_key' => 'public-key',
            'dkim_txt_name' => 'default._domainkey.mail.example.com',
            'dkim_txt_value' => 'v=DKIM1; k=rsa; p=...',
            'relay_status' => 'pending',
            'relay_last_checked_at' => null,
            'relay_error_message' => null,
        ], $overrides);
    }

    public function testList(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [$this->sampleDomain()]);

        $domains = $this->client($http)->org->domain->list();

        self::assertCount(1, $domains);
        self::assertSame('mail.example.com', $domains[0]->domain);
        self::assertSame($this->baseUrl() . '/domains', (string) $http->requests[0]->getUri());
    }

    public function testCreate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleDomain(), 201);

        $domain = $this->client($http)->org->domain->create(['domain' => 'mail.example.com']);

        self::assertSame('mail.example.com', $domain->domain);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/domains', (string) $request->getUri());
        self::assertSame(['domain' => 'mail.example.com'], json_decode((string) $request->getBody(), true));
    }

    /**
     * `/domains/{id}/verify`'s response mixes an ad-hoc `data` field with a
     * `$ref`'d `domain`, so the generator can't denormalize it as a whole -
     * `verify()` returns the decoded body as a raw array instead.
     */
    public function testVerify(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [
            'data' => ['dns_checked' => true],
            'domain' => $this->sampleDomain(['relay_status' => 'active']),
        ]);

        $result = $this->client($http)->org->domain->verify(1);

        self::assertArrayHasKey('domain', $result);
        self::assertIsArray($result['domain']);
        self::assertSame('active', $result['domain']['relay_status']);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/domains/1/verify', (string) $request->getUri());
    }

    public function testDelete(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->org->domain->delete(1);

        $request = $http->requests[0];
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame($this->baseUrl() . '/domains/1', (string) $request->getUri());
    }
}
