<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Relay;

use Hyvor\Sdk\Testing\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\RelayTestCase;

final class DomainTest extends RelayTestCase
{
    public function testList(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [$this->sampleDomain()]);

        $domains = $this->client($http)->project(self::PROJECT_ID)->domains->list();

        self::assertCount(1, $domains);
        self::assertSame('example.com', $domains[0]->domain);
        self::assertSame('active', $domains[0]->status->value);

        $request = $http->requests[0];
        self::assertSame('GET', $request->getMethod());
        self::assertSame($this->baseUrl() . '/domains', (string) $request->getUri());
        self::assertSame('42', $request->getHeaderLine('X-Project-Id'));
    }

    public function testCreate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleDomain(['domain' => 'mail.example.com']));

        $domain = $this->client($http)->project(self::PROJECT_ID)->domains->create([
            'domain' => 'mail.example.com',
        ]);

        self::assertSame('mail.example.com', $domain->domain);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/domains', (string) $request->getUri());
        self::assertSame(['domain' => 'mail.example.com'], json_decode((string) $request->getBody(), true));
        self::assertSame('42', $request->getHeaderLine('X-Project-Id'));
    }

    public function testVerify(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleDomain(['status' => 'active']));

        $domain = $this->client($http)->project(self::PROJECT_ID)->domains->verify(['id' => 1]);

        self::assertSame('active', $domain->status->value);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/domains/verify', (string) $request->getUri());
        self::assertSame(['id' => 1], json_decode((string) $request->getBody(), true));
    }

    public function testGetByIdOrDomain(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleDomain());

        $domain = $this->client($http)->project(self::PROJECT_ID)->domains->get(['domain' => 'example.com']);

        self::assertSame('example.com', $domain->domain);

        $request = $http->requests[0];
        self::assertSame('GET', $request->getMethod());
        self::assertSame($this->baseUrl() . '/domains/by', (string) $request->getUri());
        self::assertSame(['domain' => 'example.com'], json_decode((string) $request->getBody(), true));
    }

    public function testDelete(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->project(self::PROJECT_ID)->domains->delete(['id' => 1]);

        $request = $http->requests[0];
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame($this->baseUrl() . '/domains', (string) $request->getUri());
        self::assertSame(['id' => 1], json_decode((string) $request->getBody(), true));
    }
}
