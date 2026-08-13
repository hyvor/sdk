<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Talk;

use Hyvor\Sdk\Testing\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\TalkTestCase;

final class DomainsTest extends TalkTestCase
{
    public function testCreate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [
            [
                'id' => 1,
                'domain' => $this->sampleDomain(['domain' => 'blog.example.com']),
            ],
        ]);

        $domains = $this->client($http)->website(self::WEBSITE_ID)->domains->create([
            'domains' => ['blog.example.com'],
            'operation' => 'add',
        ]);

        self::assertCount(1, $domains);
        self::assertSame(1, $domains[0]->id);
        self::assertSame('blog.example.com', $domains[0]->domain->domain);
        self::assertNull($domains[0]->domain->website);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/domains', (string) $request->getUri());
        self::assertSame(
            ['domains' => ['blog.example.com'], 'operation' => 'add'],
            json_decode((string) $request->getBody(), true),
        );
        self::assertSame('Bearer test-jwt-token', $request->getHeaderLine('Authorization'));
    }

    public function testCreateWithResourceApiKeyOverridesAuth(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [
            ['id' => 1, 'domain' => $this->sampleDomain()],
        ]);

        $this->client($http)->website(self::WEBSITE_ID, 'resource-api-key')->domains->create([
            'domains' => ['example.com'],
        ]);

        self::assertSame('Bearer resource-api-key', $http->requests[0]->getHeaderLine('Authorization'));
    }
}
