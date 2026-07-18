<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Talk;

use Hyvor\Sdk\Talk\Dto\Analytics\AnalyticsGroupBy;
use Hyvor\Sdk\Talk\Dto\Analytics\CommentAnalyticsRequest;
use Hyvor\Sdk\Talk\Dto\Analytics\CreditAnalyticsRequest;
use Hyvor\Sdk\Talk\Dto\Analytics\CreditEvent;
use Hyvor\Sdk\Tests\Support\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\TalkTestCase;

final class AnalyticsTest extends TalkTestCase
{
    public function testStats(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [
            'comments' => ['total' => 100, 'last_30d' => 10],
            'users' => ['total' => 50, 'last_30d' => 5],
            'members' => ['total' => 3, 'last_30d' => 1],
            'newsletter_subscribers' => ['total' => 20, 'last_30d' => 2],
        ]);

        $stats = $this->client($http)->talk->website(self::WEBSITE_ID)->analytics->stats();

        self::assertSame(100, $stats->comments->total);
        self::assertSame(20, $stats->newsletter_subscribers->total);
        self::assertSame($this->baseUrl() . '/analytics/stats', (string) $http->requests[0]->getUri());
    }

    public function testCredits(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [
            ['date' => '2024-01-01 00:00:00', 'credits' => 5, 'events' => 2],
        ]);

        $points = $this->client($http)->talk->website(self::WEBSITE_ID)->analytics->credits(
            new CreditAnalyticsRequest(group_by: AnalyticsGroupBy::DAY, event: CreditEvent::EMBED_COMMENTS),
        );

        self::assertCount(1, $points);
        self::assertSame(5, $points[0]->credits);
        self::assertSame($this->baseUrl() . '/analytics/credits', (string) $http->requests[0]->getUri());
        self::assertSame(
            ['group_by' => 'day', 'event' => 'embed_comments'],
            json_decode((string) $http->requests[0]->getBody(), true),
        );
    }

    public function testComments(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [
            ['date' => '2024-01-01 00:00:00', 'count' => 10, 'published' => 8, 'pending' => 1, 'spam' => 1, 'deleted' => 0],
        ]);

        $points = $this->client($http)->talk->website(self::WEBSITE_ID)->analytics->comments(
            new CommentAnalyticsRequest(group_by: AnalyticsGroupBy::WEEK),
        );

        self::assertCount(1, $points);
        self::assertSame(10, $points[0]->count);
        self::assertSame($this->baseUrl() . '/analytics/comments', (string) $http->requests[0]->getUri());
    }
}
