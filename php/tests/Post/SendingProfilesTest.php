<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Post;

use Hyvor\Sdk\Post\Dto\SendingProfile\CreateSendingProfileRequest;
use Hyvor\Sdk\Post\Dto\SendingProfile\UpdateSendingProfileRequest;
use Hyvor\Sdk\Tests\Support\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\PostTestCase;

final class SendingProfilesTest extends PostTestCase
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

        $profiles = $this->client($http)->post->newsletter(self::NEWSLETTER_ID)->sendingProfiles->list();

        self::assertCount(1, $profiles);
        self::assertSame('news@example.com', $profiles[0]->from_email);
        self::assertSame($this->baseUrl() . '/sending-profiles', (string) $http->requests[0]->getUri());
    }

    public function testCreate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleSendingProfile(), 201);

        $profile = $this->client($http)->post->newsletter(self::NEWSLETTER_ID)->sendingProfiles->create(
            new CreateSendingProfileRequest(from_email: 'news@example.com', from_name: 'My Newsletter'),
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

        $profile = $this->client($http)->post->newsletter(self::NEWSLETTER_ID)->sendingProfiles->update(
            1,
            new UpdateSendingProfileRequest(is_default: true),
        );

        self::assertTrue($profile->is_default);

        $request = $http->requests[0];
        self::assertSame('PATCH', $request->getMethod());
        self::assertSame($this->baseUrl() . '/sending-profiles/1', (string) $request->getUri());
        self::assertSame(['is_default' => true], json_decode((string) $request->getBody(), true));
    }

    public function testDelete(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->post->newsletter(self::NEWSLETTER_ID)->sendingProfiles->delete(1);

        $request = $http->requests[0];
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame($this->baseUrl() . '/sending-profiles/1', (string) $request->getUri());
    }
}
