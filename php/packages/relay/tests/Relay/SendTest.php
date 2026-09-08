<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Relay;

use Hyvor\Sdk\Testing\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\RelayTestCase;

final class SendTest extends RelayTestCase
{
    public function testList(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [$this->sampleSend()]);

        $sends = $this->client($http)->project(self::PROJECT_ID)->sends->list();

        self::assertCount(1, $sends);
        self::assertSame(1, $sends[0]->id);
        self::assertSame('from@example.com', $sends[0]->from_address);

        $request = $http->requests[0];
        self::assertSame('GET', $request->getMethod());
        self::assertSame($this->baseUrl() . '/sends', (string) $request->getUri());
        self::assertSame('42', $request->getHeaderLine('X-Project-Id'));
    }

    public function testGetById(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleSend(['id' => 7]));

        $send = $this->client($http)->project(self::PROJECT_ID)->sends->get(7);

        self::assertSame(7, $send->id);

        $request = $http->requests[0];
        self::assertSame('GET', $request->getMethod());
        self::assertSame($this->baseUrl() . '/sends/7', (string) $request->getUri());
    }

    public function testSendPostsBody(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, ['id' => 10, 'message_id' => 'msg-id@example.com']);

        $result = $this->client($http)->project(self::PROJECT_ID)->sends->send([
            'from' => 'from@example.com',
            'to' => 'to@example.com',
            'subject' => 'Hi',
            'body_html' => '<p>Hello</p>',
        ]);

        self::assertSame(10, $result['id']);
        self::assertSame('msg-id@example.com', $result['message_id']);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/sends', (string) $request->getUri());
        self::assertSame(
            ['from' => 'from@example.com', 'to' => 'to@example.com', 'subject' => 'Hi', 'body_html' => '<p>Hello</p>'],
            json_decode((string) $request->getBody(), true),
        );
        self::assertSame('42', $request->getHeaderLine('X-Project-Id'));
    }

    public function testRetryPostsRecipientIds(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [
            'retried_recipients' => 2,
            'send' => $this->sampleSend(['id' => 7]),
        ]);

        $result = $this->client($http)->project(self::PROJECT_ID)->sends->retry(7, ['recipient_ids' => [1, 2]]);

        self::assertSame(2, $result['retried_recipients']);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/sends/7/retry', (string) $request->getUri());
        self::assertSame(['recipient_ids' => [1, 2]], json_decode((string) $request->getBody(), true));
    }

    public function testGetByUuid(): void
    {
        $uuid = '11111111-2222-3333-8444-555555555555';
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleSend(['id' => 7, 'uuid' => $uuid]));

        $send = $this->client($http)->project(self::PROJECT_ID)->sends->getbyuuid($uuid);

        self::assertSame($uuid, $send->uuid);

        $request = $http->requests[0];
        self::assertSame('GET', $request->getMethod());
        self::assertSame($this->baseUrl() . '/sends/uuid/' . $uuid, (string) $request->getUri());
    }
}
