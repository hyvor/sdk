<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Talk;

use Hyvor\Sdk\Talk\Dto\Sso\DeleteSsoUserRequest;
use Hyvor\Sdk\Talk\Dto\Sso\ListSsoUsersRequest;
use Hyvor\Sdk\Talk\Dto\Sso\UpsertSsoUserRequest;
use Hyvor\Sdk\Tests\Support\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\TalkTestCase;

final class SsoTest extends TalkTestCase
{
    public function testList(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [$this->sampleLoggedInUser(['type' => 'sso'])]);

        $users = $this->client($http)->talk->website(self::WEBSITE_ID)->sso->list(
            new ListSsoUsersRequest(search: 'bob'),
        );

        self::assertCount(1, $users);
        self::assertSame($this->baseUrl() . '/sso/users', (string) $http->requests[0]->getUri());
        self::assertSame(['search' => 'bob'], json_decode((string) $http->requests[0]->getBody(), true));
    }

    public function testCreateOrUpdate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleLoggedInUser(['type' => 'sso']));

        $user = $this->client($http)->talk->website(self::WEBSITE_ID)->sso->createOrUpdate(
            new UpsertSsoUserRequest(id: 'user-1', name: 'Bob', email: 'bob@example.com'),
        );

        self::assertSame('Bob', $user->name);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/sso/user', (string) $request->getUri());
        self::assertSame(
            ['id' => 'user-1', 'name' => 'Bob', 'email' => 'bob@example.com'],
            json_decode((string) $request->getBody(), true),
        );
    }

    public function testDelete(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->talk->website(self::WEBSITE_ID)->sso->delete(
            new DeleteSsoUserRequest(id: 'user-1', data: true),
        );

        $request = $http->requests[0];
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame($this->baseUrl() . '/sso/user', (string) $request->getUri());
        self::assertSame(['id' => 'user-1', 'data' => true], json_decode((string) $request->getBody(), true));
    }
}
