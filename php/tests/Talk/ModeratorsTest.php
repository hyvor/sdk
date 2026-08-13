<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Talk;

use Hyvor\Sdk\Exceptions\ValidationFailedException;
use Hyvor\Sdk\Tests\Support\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\TalkTestCase;
use Nyholm\Psr7\Response;

final class ModeratorsTest extends TalkTestCase
{
    public function testCreate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [
            'id' => 1,
            'created_at' => 1700000000,
            'role' => 'mod',
            'website_id' => self::WEBSITE_ID,
            'user' => $this->sampleAuthUser(),
        ], 201);

        $mod = $this->client($http)->talk->website(self::WEBSITE_ID)->moderators->create([
            'user_id' => 5,
        ]);

        self::assertSame(1, $mod->id);
        self::assertSame('mod', $mod->role);
        self::assertSame('Bob', $mod->user->name);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/mods', (string) $request->getUri());
        self::assertSame(['user_id' => 5], json_decode((string) $request->getBody(), true));
        self::assertSame('Bearer test-jwt-token', $request->getHeaderLine('Authorization'));
    }

    public function testCreateOnDuplicateIgnoreReturnsExistingMod(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [
            'id' => 1,
            'created_at' => 1700000000,
            'role' => 'admin',
            'website_id' => self::WEBSITE_ID,
            'user' => $this->sampleAuthUser(),
        ], 200);

        $mod = $this->client($http)->talk->website(self::WEBSITE_ID)->moderators->create([
            'user_id' => 5,
            'on_duplicate' => 'ignore',
        ]);

        self::assertSame('admin', $mod->role);
    }

    public function testCreateValidationErrorThrows(): void
    {
        $http = new FakeHttpClient();
        $http->queueResponse(new Response(422, [], json_encode([
            'message' => 'The given data was invalid.',
            'errors' => ['user_id' => ['The selected user id is invalid.']],
        ], JSON_THROW_ON_ERROR)));

        $client = $this->client($http);

        try {
            $client->talk->website(self::WEBSITE_ID)->moderators->create(['user_id' => 999999]);
            self::fail('Expected ValidationFailedException to be thrown.');
        } catch (ValidationFailedException $e) {
            self::assertSame(422, $e->statusCode);
            self::assertSame(['The selected user id is invalid.'], $e->errors['user_id']);
        }
    }

    public function testDelete(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->talk->website(self::WEBSITE_ID)->moderators->delete(['user_id' => 5]);

        $request = $http->requests[0];
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame($this->baseUrl() . '/mods', (string) $request->getUri());
        self::assertSame(['user_id' => 5], json_decode((string) $request->getBody(), true));
    }

    public function testDeleteByModId(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->talk->website(self::WEBSITE_ID)->moderators->delete(['mod_id' => 9]);

        $request = $http->requests[0];
        self::assertSame(['mod_id' => 9], json_decode((string) $request->getBody(), true));
    }
}
