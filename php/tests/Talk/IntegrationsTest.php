<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Talk;

use Hyvor\Sdk\Talk\Dto\Integration\SetSlackChannelRequest;
use Hyvor\Sdk\Tests\Support\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\TalkTestCase;

final class IntegrationsTest extends TalkTestCase
{
    public function testSlackStatus(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [
            'connection' => ['has_token' => true, 'channel_name' => 'general', 'channel_id' => 'C123'],
        ]);

        $status = $this->client($http)->talk->website(self::WEBSITE_ID)->integrations->slack->status();

        self::assertTrue($status->connection->has_token);
        self::assertSame('general', $status->connection->channel_name);
        self::assertSame($this->baseUrl() . '/integrations/slack', (string) $http->requests[0]->getUri());
    }

    public function testSlackInit(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, ['url' => 'https://slack.com/oauth/authorize?...']);

        $response = $this->client($http)->talk->website(self::WEBSITE_ID)->integrations->slack->init();

        self::assertStringStartsWith('https://slack.com', $response->url);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/integrations/slack', (string) $request->getUri());
    }

    public function testSlackSetChannel(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->talk->website(self::WEBSITE_ID)->integrations->slack->setChannel(
            new SetSlackChannelRequest(channel: 'general'),
        );

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/integrations/slack/channel', (string) $request->getUri());
        self::assertSame(['channel' => 'general'], json_decode((string) $request->getBody(), true));
    }

    public function testSlackDisconnect(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->talk->website(self::WEBSITE_ID)->integrations->slack->disconnect();

        $request = $http->requests[0];
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame($this->baseUrl() . '/integrations/slack', (string) $request->getUri());
    }
}
