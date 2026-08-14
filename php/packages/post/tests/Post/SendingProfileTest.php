<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Post;

use Hyvor\Sdk\Testing\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\PostTestCase;

final class SendingProfileTest extends PostTestCase
{
    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function sampleSendingProfile(array $overrides = []): array
    {
        return array_merge([
            'id' => 1,
            'created_at' => 1700000000,
            'from_email' => 'news@example.com',
            'from_name' => 'My Newsletter',
            'reply_to_email' => null,
            'brand_name' => null,
            'brand_logo' => null,
            'brand_url' => null,
            'is_default' => true,
            'is_system' => false,
        ], $overrides);
    }

    public function testList(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [$this->sampleSendingProfile()]);

        $profiles = $this->client($http)->newsletter(self::NEWSLETTER_ID)->sending_profiles->list();

        self::assertCount(1, $profiles);
        self::assertSame('news@example.com', $profiles[0]->from_email);
        self::assertSame($this->baseUrl() . '/sending-profiles', (string) $http->requests[0]->getUri());
    }

    public function testCreate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleSendingProfile(), 201);

        $profile = $this->client($http)->newsletter(self::NEWSLETTER_ID)->sending_profiles->create(
            ['from_email' => 'news@example.com', 'from_name' => 'My Newsletter'],
        );

        self::assertSame('news@example.com', $profile->from_email);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/sending-profiles', (string) $request->getUri());
        self::assertSame(
            ['from_email' => 'news@example.com', 'from_name' => 'My Newsletter'],
            json_decode((string) $request->getBody(), true),
        );
    }

    public function testUpdate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleSendingProfile(['is_default' => true]));

        $profile = $this->client($http)->newsletter(self::NEWSLETTER_ID)->sending_profiles->update(
            1,
            ['from_email' => 'news@example.com', 'is_default' => true],
        );

        self::assertTrue($profile->is_default);

        $request = $http->requests[0];
        self::assertSame('PATCH', $request->getMethod());
        self::assertSame($this->baseUrl() . '/sending-profiles/1', (string) $request->getUri());
        self::assertSame(
            ['from_email' => 'news@example.com', 'is_default' => true],
            json_decode((string) $request->getBody(), true),
        );
    }

    public function testDelete(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [$this->sampleSendingProfile(['id' => 2])]);

        $remaining = $this->client($http)->newsletter(self::NEWSLETTER_ID)->sending_profiles->delete(1);

        self::assertCount(1, $remaining);
        self::assertSame(2, $remaining[0]->id);

        $request = $http->requests[0];
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame($this->baseUrl() . '/sending-profiles/1', (string) $request->getUri());
    }
}
