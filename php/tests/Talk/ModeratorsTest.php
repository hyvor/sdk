<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Talk;

use Hyvor\Sdk\Talk\Dto\Moderator\AddModeratorRequest;
use Hyvor\Sdk\Talk\Dto\Moderator\DeleteModeratorRequest;
use Hyvor\Sdk\Talk\Dto\Moderator\ModeratorOnDuplicate;
use Hyvor\Sdk\Talk\Dto\Moderator\UpdateModeratorRequest;
use Hyvor\Sdk\Talk\Dto\User\UserRole;
use Hyvor\Sdk\Tests\Support\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\TalkTestCase;

final class ModeratorsTest extends TalkTestCase
{
    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function sampleMod(array $overrides = []): array
    {
        return array_merge([
            'id' => 1,
            'created_at' => 1700000000,
            'role' => 'mod',
            'user' => $this->sampleUserMini(),
            'sso_user' => null,
            'is_alias_used' => false,
        ], $overrides);
    }

    public function testList(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [$this->sampleMod()]);

        $mods = $this->client($http)->talk->website(self::WEBSITE_ID)->moderators->list();

        self::assertCount(1, $mods);
        self::assertSame(UserRole::MOD, $mods[0]->role);
        self::assertSame($this->baseUrl() . '/moderators', (string) $http->requests[0]->getUri());
    }

    public function testCreate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleMod());

        $mod = $this->client($http)->talk->website(self::WEBSITE_ID)->moderators->create(
            new AddModeratorRequest(user_id: 9, role: UserRole::ADMIN, on_duplicate: ModeratorOnDuplicate::UPDATE),
        );

        self::assertSame(1, $mod->id);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/moderators', (string) $request->getUri());
        self::assertSame(
            ['user_id' => 9, 'role' => 'admin', 'on_duplicate' => 'update'],
            json_decode((string) $request->getBody(), true),
        );
    }

    public function testUpdate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleMod(['role' => 'admin']));

        $mod = $this->client($http)->talk->website(self::WEBSITE_ID)->moderators->update(
            new UpdateModeratorRequest(mod_id: 1, role: UserRole::ADMIN),
        );

        self::assertSame(UserRole::ADMIN, $mod->role);

        $request = $http->requests[0];
        self::assertSame('PATCH', $request->getMethod());
        self::assertSame($this->baseUrl() . '/moderators', (string) $request->getUri());
    }

    public function testDelete(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->talk->website(self::WEBSITE_ID)->moderators->delete(
            new DeleteModeratorRequest(mod_id: 1),
        );

        $request = $http->requests[0];
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame($this->baseUrl() . '/moderators', (string) $request->getUri());
        self::assertSame(['mod_id' => 1], json_decode((string) $request->getBody(), true));
    }
}
