<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Post;

use Hyvor\Sdk\Post\Dto\Export\SubscriberExportStatus;
use Hyvor\Sdk\Tests\Support\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\PostTestCase;

final class ExportsTest extends PostTestCase
{
    public function testList(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [
            [
                'id' => 1,
                'created_at' => 1700000000,
                'status' => 'completed',
                'error_message' => null,
                'url' => 'https://cdn.example.com/export.csv',
            ],
        ]);

        $exports = $this->client($http)->post->newsletter(self::NEWSLETTER_ID)->exports->list();

        self::assertCount(1, $exports);
        self::assertSame(SubscriberExportStatus::COMPLETED, $exports[0]->status);
        self::assertSame($this->baseUrl() . '/export', (string) $http->requests[0]->getUri());
    }

    public function testCreate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [
            'id' => 1,
            'created_at' => 1700000000,
            'status' => 'pending',
            'error_message' => null,
            'url' => null,
        ], 201);

        $export = $this->client($http)->post->newsletter(self::NEWSLETTER_ID)->exports->create();

        self::assertSame(SubscriberExportStatus::PENDING, $export->status);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/export', (string) $request->getUri());
    }
}
