<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Talk;

use Hyvor\Sdk\Talk\Dto\Webhook\CreateWebhookRequest;
use Hyvor\Sdk\Talk\Dto\Webhook\UpdateWebhookRequest;
use Hyvor\Sdk\Talk\Dto\Webhook\WebhookEvent;
use Hyvor\Sdk\Tests\Support\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\TalkTestCase;

final class WebhooksTest extends TalkTestCase
{
    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function sampleWebhookConfiguration(array $overrides = []): array
    {
        return array_merge([
            'id' => 1,
            'url' => 'https://example.com/hook',
            'events' => ['comment.create'],
            'secret' => 'shh',
        ], $overrides);
    }

    public function testList(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [$this->sampleWebhookConfiguration()]);

        $webhooks = $this->client($http)->talk->website(self::WEBSITE_ID)->webhooks->list();

        self::assertCount(1, $webhooks);
        self::assertSame([WebhookEvent::COMMENT_CREATE], $webhooks[0]->events);
        self::assertSame($this->baseUrl() . '/webhooks', (string) $http->requests[0]->getUri());
    }

    public function testCreate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleWebhookConfiguration());

        $webhook = $this->client($http)->talk->website(self::WEBSITE_ID)->webhooks->create(
            new CreateWebhookRequest(url: 'https://example.com/hook', events: [WebhookEvent::COMMENT_CREATE]),
        );

        self::assertSame(1, $webhook->id);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/webhook', (string) $request->getUri());
        self::assertSame(
            ['url' => 'https://example.com/hook', 'events' => ['comment.create']],
            json_decode((string) $request->getBody(), true),
        );
    }

    public function testUpdate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleWebhookConfiguration(['url' => 'https://example.com/new-hook']));

        $webhook = $this->client($http)->talk->website(self::WEBSITE_ID)->webhooks->update(
            1,
            new UpdateWebhookRequest(url: 'https://example.com/new-hook'),
        );

        self::assertSame('https://example.com/new-hook', $webhook->url);
        self::assertSame($this->baseUrl() . '/webhook/1', (string) $http->requests[0]->getUri());
    }

    public function testDelete(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->talk->website(self::WEBSITE_ID)->webhooks->delete(1);

        $request = $http->requests[0];
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame($this->baseUrl() . '/webhook/1', (string) $request->getUri());
    }

    public function testDeliveries(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [
            [
                'id' => 1, 'created_at' => 1700000000, 'webhook_configuration_id' => 1,
                'url' => 'https://example.com/hook', 'event' => 'comment.create', 'status' => 'completed',
                'request_body' => '{}', 'num_attempts' => 1, 'last_attempt_at' => 1700000001,
                'response_body' => 'ok', 'response_code' => 200,
            ],
        ]);

        $deliveries = $this->client($http)->talk->website(self::WEBSITE_ID)->webhooks->deliveries();

        self::assertCount(1, $deliveries);
        self::assertSame(200, $deliveries[0]->response_code);
        self::assertSame($this->baseUrl() . '/webhook/deliveries', (string) $http->requests[0]->getUri());
    }
}
