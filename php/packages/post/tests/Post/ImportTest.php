<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Post;

use Hyvor\Sdk\Testing\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\PostTestCase;

final class ImportTest extends PostTestCase
{
    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function sampleImport(array $overrides = []): array
    {
        return array_merge([
            'id' => 1,
            'created_at' => 1700000000,
            'status' => 'requires_input',
            'fields' => null,
            'csv_fields' => ['email', 'name'],
            'imported_subscribers' => null,
            'warnings' => null,
            'error_message' => null,
        ], $overrides);
    }

    public function testUpload(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleImport());

        $import = $this->client($http)->newsletter(self::NEWSLETTER_ID)->imports->upload(
            ['source' => 'https://example.com/subscribers.csv'],
        );

        self::assertSame(['email', 'name'], $import->csv_fields);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/imports/upload', (string) $request->getUri());
        self::assertSame(
            ['source' => 'https://example.com/subscribers.csv'],
            json_decode((string) $request->getBody(), true),
        );
    }

    public function testStart(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleImport(['status' => 'importing']));

        $import = $this->client($http)->newsletter(self::NEWSLETTER_ID)->imports->start(
            1,
            ['mapping' => ['email' => 'email', 'name' => 'name']],
        );

        self::assertSame('importing', $import->status->value);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/imports/1', (string) $request->getUri());
    }

    public function testList(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [$this->sampleImport()]);

        $imports = $this->client($http)->newsletter(self::NEWSLETTER_ID)->imports->list();

        self::assertCount(1, $imports);
        self::assertSame($this->baseUrl() . '/imports', (string) $http->requests[0]->getUri());
    }

    /**
     * `/imports/limits`'s response is an inline shape (not a named schema),
     * so `getlimits()` returns the decoded body as a raw array instead of a
     * typed DTO.
     */
    public function testGetlimits(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, ['daily_limit_exceeded' => false, 'monthly_limit_exceeded' => true]);

        $limits = $this->client($http)->newsletter(self::NEWSLETTER_ID)->imports->getlimits();

        self::assertFalse($limits['daily_limit_exceeded']);
        self::assertTrue($limits['monthly_limit_exceeded']);
        self::assertSame($this->baseUrl() . '/imports/limits', (string) $http->requests[0]->getUri());
    }
}
