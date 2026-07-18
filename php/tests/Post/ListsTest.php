<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Post;

use Hyvor\Sdk\Post\Dto\List\CreateListRequest;
use Hyvor\Sdk\Post\Dto\List\UpdateListRequest;
use Hyvor\Sdk\Tests\Support\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\PostTestCase;

final class ListsTest extends PostTestCase
{
    public function testCreate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [
            'id' => 1,
            'created_at' => 1700000000,
            'name' => 'Default',
            'description' => null,
            'subscribers_count' => 0,
        ], 201);

        $list = $this->client($http)->post->newsletter(self::NEWSLETTER_ID)->lists->create(
            new CreateListRequest(name: 'Default'),
        );

        self::assertSame('Default', $list->name);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/lists', (string) $request->getUri());
        self::assertSame(['name' => 'Default'], json_decode((string) $request->getBody(), true));
    }

    public function testUpdate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [
            'id' => 1,
            'created_at' => 1700000000,
            'name' => 'Renamed',
            'description' => 'New description',
            'subscribers_count' => 5,
        ]);

        $list = $this->client($http)->post->newsletter(self::NEWSLETTER_ID)->lists->update(
            1,
            new UpdateListRequest(name: 'Renamed', description: 'New description'),
        );

        self::assertSame('Renamed', $list->name);
        self::assertSame(5, $list->subscribers_count);

        $request = $http->requests[0];
        self::assertSame('PATCH', $request->getMethod());
        self::assertSame($this->baseUrl() . '/lists/1', (string) $request->getUri());
    }

    public function testDelete(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->post->newsletter(self::NEWSLETTER_ID)->lists->delete(1);

        $request = $http->requests[0];
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame($this->baseUrl() . '/lists/1', (string) $request->getUri());
    }
}
