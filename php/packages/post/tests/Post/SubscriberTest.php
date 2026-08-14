<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Post;

use Hyvor\Sdk\Testing\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\PostTestCase;

final class SubscriberTest extends PostTestCase
{
    public function testList(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [$this->sampleSubscriber()]);

        $subscribers = $this->client($http)->newsletter(self::NEWSLETTER_ID)->subscribers->list();

        self::assertCount(1, $subscribers);
        self::assertSame('jane@example.com', $subscribers[0]->email);
        self::assertSame($this->baseUrl() . '/subscribers', (string) $http->requests[0]->getUri());
    }

    public function testGetbyemail(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleSubscriber());

        $subscriber = $this->client($http)->newsletter(self::NEWSLETTER_ID)->subscribers->getbyemail('jane@example.com');

        self::assertSame('jane@example.com', $subscriber->email);
        self::assertSame($this->baseUrl() . '/subscribers/email/jane%40example.com', (string) $http->requests[0]->getUri());
    }

    public function testCreate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleSubscriber(), 201);

        $subscriber = $this->client($http)->newsletter(self::NEWSLETTER_ID)->subscribers->create(
            [
                'email' => 'jane@example.com',
                'lists' => ['Default'],
                'lists_strategy' => 'merge',
                // required per `CreateSubscriberInput`, alongside the
                // snake_case `list_skip_resubscribe_on` - looks like a spec
                // bug (a leftover camelCase duplicate), but included here to
                // satisfy the generated array-shape type.
                'listSkipResubscribeOn' => ['unsubscribe'],
                'listResubscribeOnValues' => ['tag1'],
            ],
        );

        self::assertSame('jane@example.com', $subscriber->email);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/subscribers', (string) $request->getUri());
        self::assertSame(
            [
                'email' => 'jane@example.com',
                'lists' => ['Default'],
                'lists_strategy' => 'merge',
                'listSkipResubscribeOn' => ['unsubscribe'],
                'listResubscribeOnValues' => ['tag1'],
            ],
            json_decode((string) $request->getBody(), true),
        );
    }

    public function testDelete(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->newsletter(self::NEWSLETTER_ID)->subscribers->delete(1);

        $request = $http->requests[0];
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame($this->baseUrl() . '/subscribers/1', (string) $request->getUri());
    }

    /**
     * `/subscribers/bulk`'s response is an inline, ad-hoc shape (not a named
     * schema), so the generator can't denormalize it to a DTO - `bulk()`
     * returns the decoded body as a raw array instead.
     */
    public function testBulk(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [
            'status' => 'ok',
            'message' => 'Updated 2 subscribers',
            'subscribers' => [$this->sampleSubscriber(), $this->sampleSubscriber(['id' => 2])],
        ]);

        $response = $this->client($http)->newsletter(self::NEWSLETTER_ID)->subscribers->bulk(
            [
                'subscribers_ids' => [1, 2],
                'action' => 'status_change',
                'status' => 'unsubscribed',
                'metadata' => [],
            ],
        );

        self::assertSame('ok', $response['status']);
        self::assertIsArray($response['subscribers']);
        self::assertCount(2, $response['subscribers']);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/subscribers/bulk', (string) $request->getUri());
        self::assertSame(
            ['subscribers_ids' => [1, 2], 'action' => 'status_change', 'status' => 'unsubscribed', 'metadata' => []],
            json_decode((string) $request->getBody(), true),
        );
    }
}
