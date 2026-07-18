<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Talk;

use Hyvor\Sdk\Talk\Dto\Ip\UpdateIpRequest;
use Hyvor\Sdk\Talk\Dto\User\UserState;
use Hyvor\Sdk\Tests\Support\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\TalkTestCase;

final class IpsTest extends TalkTestCase
{
    public function testList(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [
            ['ip' => '1.2.3.4', 'note' => null, 'state' => 'banned', 'state_ends_at' => null],
        ]);

        $ips = $this->client($http)->talk->website(self::WEBSITE_ID)->ips->list();

        self::assertCount(1, $ips);
        self::assertSame(UserState::BANNED, $ips[0]->state);
        self::assertSame($this->baseUrl() . '/ips', (string) $http->requests[0]->getUri());
    }

    public function testGet(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, ['ip' => '1.2.3.4', 'note' => null, 'state' => 'default', 'state_ends_at' => null]);

        $ip = $this->client($http)->talk->website(self::WEBSITE_ID)->ips->get('1.2.3.4');

        self::assertSame('1.2.3.4', $ip->ip);
        self::assertSame($this->baseUrl() . '/ip/1.2.3.4', (string) $http->requests[0]->getUri());
    }

    public function testUpdate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, ['ip' => '1.2.3.4', 'note' => 'suspicious', 'state' => 'shadowed', 'state_ends_at' => null]);

        $ip = $this->client($http)->talk->website(self::WEBSITE_ID)->ips->update(
            '1.2.3.4',
            new UpdateIpRequest(state: UserState::SHADOWED, note: 'suspicious'),
        );

        self::assertSame(UserState::SHADOWED, $ip->state);

        $request = $http->requests[0];
        self::assertSame('PATCH', $request->getMethod());
        self::assertSame($this->baseUrl() . '/ip/1.2.3.4', (string) $request->getUri());
        self::assertSame(
            ['state' => 'shadowed', 'note' => 'suspicious'],
            json_decode((string) $request->getBody(), true),
        );
    }
}
