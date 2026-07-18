<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Post;

use Hyvor\Sdk\Tests\Support\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\PostTestCase;

final class InvitesTest extends PostTestCase
{
    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function sampleInvite(array $overrides = []): array
    {
        return array_merge([
            'id' => 1,
            'created_at' => 1700000000,
            'role' => 'admin',
            'user' => $this->sampleUserMini(),
            'expires_at' => 1700100000,
        ], $overrides);
    }

    public function testList(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [$this->sampleInvite()]);

        $invites = $this->client($http)->post->newsletter(self::NEWSLETTER_ID)->invites->list();

        self::assertCount(1, $invites);
        self::assertSame($this->baseUrl() . '/invites', (string) $http->requests[0]->getUri());
    }

    public function testCreate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleInvite(), 201);

        $invite = $this->client($http)->post->newsletter(self::NEWSLETTER_ID)->invites->create(
            ['email' => 'bob@example.com'],
        );

        self::assertSame('Bob', $invite->user->name);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/invites', (string) $request->getUri());
        self::assertSame(['email' => 'bob@example.com'], json_decode((string) $request->getBody(), true));
    }

    public function testDelete(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->post->newsletter(self::NEWSLETTER_ID)->invites->delete(1);

        $request = $http->requests[0];
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame($this->baseUrl() . '/invites/1', (string) $request->getUri());
    }
}
