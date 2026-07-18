<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Talk;

use Hyvor\Sdk\Talk\Dto\User\ListUsersRequest;
use Hyvor\Sdk\Talk\Dto\User\UpdateEmailNotificationRequest;
use Hyvor\Sdk\Talk\Dto\User\UpdateUserRequest;
use Hyvor\Sdk\Talk\Dto\User\UserState;
use Hyvor\Sdk\Tests\Support\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\TalkTestCase;

final class UsersTest extends TalkTestCase
{
    public function testList(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [$this->sampleLoggedInUser()]);

        $users = $this->client($http)->talk->website(self::WEBSITE_ID)->users->list(
            new ListUsersRequest(state: UserState::BANNED),
        );

        self::assertCount(1, $users);
        self::assertSame($this->baseUrl() . '/users', (string) $http->requests[0]->getUri());
        self::assertSame(['state' => 'banned'], json_decode((string) $http->requests[0]->getBody(), true));
    }

    public function testGetByHtid(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleLoggedInUser());

        $user = $this->client($http)->talk->website(self::WEBSITE_ID)->users->get('hyvor_1');

        self::assertSame('Bob', $user->name);
        self::assertSame($this->baseUrl() . '/user/hyvor_1', (string) $http->requests[0]->getUri());
        self::assertSame('', $http->requests[0]->getHeaderLine('X-ID-Type'));
    }

    public function testGetBySsoId(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleLoggedInUser());

        $this->client($http)->talk->website(self::WEBSITE_ID)->users->get('sso-user-1', bySsoId: true);

        self::assertSame('sso_user_id', $http->requests[0]->getHeaderLine('X-ID-Type'));
    }

    public function testUpdate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleLoggedInUser(['state' => 'trusted']));

        $user = $this->client($http)->talk->website(self::WEBSITE_ID)->users->update(
            'hyvor_1',
            new UpdateUserRequest(state: UserState::TRUSTED),
        );

        self::assertSame(UserState::TRUSTED, $user->state);

        $request = $http->requests[0];
        self::assertSame('PATCH', $request->getMethod());
        self::assertSame($this->baseUrl() . '/user/hyvor_1', (string) $request->getUri());
        self::assertSame(['state' => 'trusted'], json_decode((string) $request->getBody(), true));
    }

    public function testCounts(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [
            'comments' => ['total' => 10, 'published' => 8, 'pending' => 1, 'spam' => 1, 'deleted' => 0],
            'flags' => ['received' => 2, 'given' => 1],
        ]);

        $counts = $this->client($http)->talk->website(self::WEBSITE_ID)->users->counts('hyvor_1');

        self::assertSame(10, $counts->comments->total);
        self::assertSame(2, $counts->flags->received);
        self::assertSame($this->baseUrl() . '/user/hyvor_1/counts', (string) $http->requests[0]->getUri());
    }

    public function testDelete(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->talk->website(self::WEBSITE_ID)->users->delete('hyvor_1', data: true);

        $request = $http->requests[0];
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame($this->baseUrl() . '/user/hyvor_1', (string) $request->getUri());
        self::assertSame(['data' => true], json_decode((string) $request->getBody(), true));
    }

    public function testGetEmailNotification(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, ['reply' => true, 'mention' => false]);

        $status = $this->client($http)->talk->website(self::WEBSITE_ID)->users->getEmailNotification('hyvor_1');

        self::assertTrue($status->reply);
        self::assertFalse($status->mention);
        self::assertSame($this->baseUrl() . '/user/hyvor_1/email-notification', (string) $http->requests[0]->getUri());
    }

    public function testUpdateEmailNotification(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->talk->website(self::WEBSITE_ID)->users->updateEmailNotification(
            'hyvor_1',
            new UpdateEmailNotificationRequest(reply: false),
        );

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/user/hyvor_1/email-notification', (string) $request->getUri());
        self::assertSame(['reply' => false], json_decode((string) $request->getBody(), true));
    }
}
