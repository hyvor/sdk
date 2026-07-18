<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Talk;

use Hyvor\Sdk\Talk\Dto\EmailDomain\CreateEmailDomainRequest;
use Hyvor\Sdk\Tests\Support\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\TalkTestCase;

final class EmailDomainTest extends TalkTestCase
{
    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function sampleEmailDomain(array $overrides = []): array
    {
        return array_merge([
            'id' => 1,
            'domain' => 'mail.example.com',
            'dkim_public_key' => 'pubkey',
            'dkim_txt_name' => 'dkim._domainkey.mail.example.com',
            'dkim_txt_value' => 'v=DKIM1; ...',
            'verified' => false,
            'verified_in_ses' => false,
            'requested_by_current_website' => true,
        ], $overrides);
    }

    public function testCreate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleEmailDomain());

        $domain = $this->client($http)->talk->website(self::WEBSITE_ID)->emailDomain->create(
            new CreateEmailDomainRequest(domain: 'mail.example.com'),
        );

        self::assertSame('mail.example.com', $domain->domain);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/email/domain', (string) $request->getUri());
        self::assertSame(['domain' => 'mail.example.com'], json_decode((string) $request->getBody(), true));
    }

    public function testVerify(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [
            'data' => ['verified' => true, 'debug' => null],
            'domain' => $this->sampleEmailDomain(['verified' => true]),
        ]);

        $result = $this->client($http)->talk->website(self::WEBSITE_ID)->emailDomain->verify();

        self::assertTrue($result->data->verified);
        self::assertTrue($result->domain->verified);
        self::assertSame($this->baseUrl() . '/email/domain/verify', (string) $http->requests[0]->getUri());
    }

    public function testVerifyWithDebugInfo(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [
            'data' => ['verified' => false, 'debug' => ['last_checked_at' => '2024-01-01T00:00:00Z', 'error_type' => 'dns_not_found']],
            'domain' => $this->sampleEmailDomain(),
        ]);

        $result = $this->client($http)->talk->website(self::WEBSITE_ID)->emailDomain->verify();

        self::assertFalse($result->data->verified);
        self::assertNotNull($result->data->debug);
        self::assertSame('dns_not_found', $result->data->debug->error_type);
    }

    public function testDelete(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->talk->website(self::WEBSITE_ID)->emailDomain->delete();

        $request = $http->requests[0];
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame($this->baseUrl() . '/email/domain', (string) $request->getUri());
    }
}
