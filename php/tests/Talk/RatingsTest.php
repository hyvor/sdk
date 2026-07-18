<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Talk;

use Hyvor\Sdk\Talk\Dto\Rating\ListRatingsRequest;
use Hyvor\Sdk\Tests\Support\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\TalkTestCase;

final class RatingsTest extends TalkTestCase
{
    public function testList(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [
            ['id' => 1, 'created_at' => 1700000000, 'page' => $this->samplePage(), 'user' => $this->sampleGuestUser(), 'rating' => 5],
        ]);

        $ratings = $this->client($http)->talk->website(self::WEBSITE_ID)->ratings->list(
            new ListRatingsRequest(rating: 5),
        );

        self::assertCount(1, $ratings);
        self::assertSame(5, $ratings[0]->rating);
        self::assertSame($this->baseUrl() . '/ratings', (string) $http->requests[0]->getUri());
        self::assertSame(['rating' => 5], json_decode((string) $http->requests[0]->getBody(), true));
    }

    public function testDelete(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->talk->website(self::WEBSITE_ID)->ratings->delete(3);

        $request = $http->requests[0];
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame($this->baseUrl() . '/rating/3', (string) $request->getUri());
    }
}
