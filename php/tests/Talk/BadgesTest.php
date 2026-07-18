<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Talk;

use Hyvor\Sdk\Talk\Dto\Badge\CreateBadgeRequest;
use Hyvor\Sdk\Talk\Dto\Badge\UpdateBadgeRequest;
use Hyvor\Sdk\Tests\Support\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\TalkTestCase;

final class BadgesTest extends TalkTestCase
{
    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function sampleBadge(array $overrides = []): array
    {
        return array_merge([
            'id' => 1,
            'text' => 'VIP',
            'background_color' => '#0000ff',
            'color' => '#ffffff',
            'icon_url' => null,
        ], $overrides);
    }

    public function testList(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [$this->sampleBadge()]);

        $badges = $this->client($http)->talk->website(self::WEBSITE_ID)->badges->list();

        self::assertCount(1, $badges);
        self::assertSame($this->baseUrl() . '/badges', (string) $http->requests[0]->getUri());
    }

    public function testCreate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleBadge());

        $badge = $this->client($http)->talk->website(self::WEBSITE_ID)->badges->create(
            new CreateBadgeRequest(text: 'VIP', color: '#ffffff', background_color: '#0000ff'),
        );

        self::assertSame('VIP', $badge->text);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/badge', (string) $request->getUri());
    }

    public function testUpdate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleBadge(['text' => 'Legend']));

        $badge = $this->client($http)->talk->website(self::WEBSITE_ID)->badges->update(
            1,
            new UpdateBadgeRequest(text: 'Legend'),
        );

        self::assertSame('Legend', $badge->text);
        self::assertSame($this->baseUrl() . '/badge/1', (string) $http->requests[0]->getUri());
    }

    public function testDelete(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->talk->website(self::WEBSITE_ID)->badges->delete(1);

        $request = $http->requests[0];
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame($this->baseUrl() . '/badge/1', (string) $request->getUri());
    }
}
