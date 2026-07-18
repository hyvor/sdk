<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Post;

use Hyvor\Sdk\Post\Dto\User\UserRole;
use Hyvor\Sdk\Tests\Support\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\PostTestCase;

final class UsersTest extends PostTestCase
{
    public function testList(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [
            [
                'id' => 1,
                'role' => 'owner',
                'created_at' => 1700000000,
                'user' => $this->sampleUserMini(),
            ],
        ]);

        $users = $this->client($http)->post->newsletter(self::NEWSLETTER_ID)->users->list();

        self::assertCount(1, $users);
        self::assertSame(UserRole::OWNER, $users[0]->role);
        self::assertSame('Bob', $users[0]->user->name);
        self::assertSame($this->baseUrl() . '/users', (string) $http->requests[0]->getUri());
    }

    public function testDelete(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->post->newsletter(self::NEWSLETTER_ID)->users->delete(1);

        $request = $http->requests[0];
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame($this->baseUrl() . '/users/1', (string) $request->getUri());
    }
}
