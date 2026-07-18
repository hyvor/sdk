<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Post;

use Hyvor\Sdk\Post\Dto\Subscriber\BulkSubscriberAction;
use Hyvor\Sdk\Post\Dto\Subscriber\BulkUpdateSubscribersRequest;
use Hyvor\Sdk\Post\Dto\Subscriber\CreateOrUpdateSubscriberRequest;
use Hyvor\Sdk\Post\Dto\Subscriber\ListsStrategy;
use Hyvor\Sdk\Post\Dto\Subscriber\ListSubscribersRequest;
use Hyvor\Sdk\Post\Dto\Subscriber\SubscriberStatusFilter;
use Hyvor\Sdk\Tests\Support\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\PostTestCase;

final class SubscribersTest extends PostTestCase
{
    public function testList(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [$this->sampleSubscriber()]);

        $subscribers = $this->client($http)->post->newsletter(self::NEWSLETTER_ID)->subscribers->list(
            new ListSubscribersRequest(status: SubscriberStatusFilter::SUBSCRIBED, list_id: 1),
        );

        self::assertCount(1, $subscribers);
        self::assertSame('jane@example.com', $subscribers[0]->email);
        self::assertSame($this->baseUrl() . '/subscribers', (string) $http->requests[0]->getUri());
        self::assertSame(
            ['status' => 'subscribed', 'list_id' => 1],
            json_decode((string) $http->requests[0]->getBody(), true),
        );
    }

    public function testGetByEmail(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleSubscriber());

        $subscriber = $this->client($http)->post->newsletter(self::NEWSLETTER_ID)->subscribers->getByEmail('jane@example.com');

        self::assertSame('jane@example.com', $subscriber->email);
        self::assertSame($this->baseUrl() . '/subscribers/email/jane%40example.com', (string) $http->requests[0]->getUri());
    }

    public function testCreateOrUpdate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleSubscriber(), 201);

        $subscriber = $this->client($http)->post->newsletter(self::NEWSLETTER_ID)->subscribers->createOrUpdate(
            new CreateOrUpdateSubscriberRequest(
                email: 'jane@example.com',
                lists: ['Default'],
                lists_strategy: ListsStrategy::MERGE,
            ),
        );

        self::assertSame('jane@example.com', $subscriber->email);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/subscribers', (string) $request->getUri());
        self::assertSame(
            ['email' => 'jane@example.com', 'lists' => ['Default'], 'lists_strategy' => 'merge'],
            json_decode((string) $request->getBody(), true),
        );
    }

    public function testDelete(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->post->newsletter(self::NEWSLETTER_ID)->subscribers->delete(1);

        $request = $http->requests[0];
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame($this->baseUrl() . '/subscribers/1', (string) $request->getUri());
    }

    public function testBulkUpdate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [
            'status' => 'ok',
            'message' => 'Updated 2 subscribers',
            'subscribers' => [$this->sampleSubscriber(), $this->sampleSubscriber(['id' => 2])],
        ]);

        $response = $this->client($http)->post->newsletter(self::NEWSLETTER_ID)->subscribers->bulkUpdate(
            new BulkUpdateSubscribersRequest(
                subscribers_ids: [1, 2],
                action: BulkSubscriberAction::STATUS_CHANGE,
                status: SubscriberStatusFilter::UNSUBSCRIBED,
            ),
        );

        self::assertSame('ok', $response->status);
        self::assertCount(2, $response->subscribers);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/subscribers/bulk', (string) $request->getUri());
        self::assertSame(
            ['subscribers_ids' => [1, 2], 'action' => 'status_change', 'status' => 'unsubscribed'],
            json_decode((string) $request->getBody(), true),
        );
    }
}
