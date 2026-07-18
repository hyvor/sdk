<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Talk;

use Hyvor\Sdk\Talk\Dto\Domain\CreateDomainsRequest;
use Hyvor\Sdk\Talk\Dto\Domain\DeleteDomainRequest;
use Hyvor\Sdk\Talk\Dto\Domain\DomainOnDuplicate;
use Hyvor\Sdk\Talk\Dto\Domain\UpdateDomainRequest;
use Hyvor\Sdk\Tests\Support\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\TalkTestCase;

final class DomainsTest extends TalkTestCase
{
    public function testList(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [['id' => 1, 'domain' => 'example.com']]);

        $domains = $this->client($http)->talk->website(self::WEBSITE_ID)->domains->list();

        self::assertCount(1, $domains);
        self::assertSame($this->baseUrl() . '/domains', (string) $http->requests[0]->getUri());
    }

    public function testCreate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [['id' => 1, 'domain' => 'example.com'], ['id' => 2, 'domain' => 'blog.example.com']]);

        $domains = $this->client($http)->talk->website(self::WEBSITE_ID)->domains->create(
            new CreateDomainsRequest(domains: ['example.com', 'blog.example.com'], on_duplicate: DomainOnDuplicate::IGNORE),
        );

        self::assertCount(2, $domains);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/domains', (string) $request->getUri());
        self::assertSame(
            ['domains' => ['example.com', 'blog.example.com'], 'on_duplicate' => 'ignore'],
            json_decode((string) $request->getBody(), true),
        );
    }

    public function testUpdate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, ['id' => 1, 'domain' => 'new.example.com']);

        $domain = $this->client($http)->talk->website(self::WEBSITE_ID)->domains->update(
            new UpdateDomainRequest(old_domain: 'old.example.com', new_domain: 'new.example.com'),
        );

        self::assertSame('new.example.com', $domain->domain);

        $request = $http->requests[0];
        self::assertSame('PATCH', $request->getMethod());
        self::assertSame($this->baseUrl() . '/domains', (string) $request->getUri());
    }

    public function testDelete(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->talk->website(self::WEBSITE_ID)->domains->delete(
            new DeleteDomainRequest(domain: 'example.com'),
        );

        $request = $http->requests[0];
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame($this->baseUrl() . '/domains', (string) $request->getUri());
        self::assertSame(['domain' => 'example.com'], json_decode((string) $request->getBody(), true));
    }
}
