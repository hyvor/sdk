<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Talk;

use Hyvor\Sdk\Auth\StaticTokenProvider;
use Hyvor\Sdk\Exceptions\ValidationFailedException;
use Hyvor\Sdk\Talk\TalkClient;
use Hyvor\Sdk\Testing\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\TalkTestCase;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;

final class WebsiteTest extends TalkTestCase
{
    public function testCreateSendsNameAndDomain(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [
            'id' => 7,
            'name' => 'My Blog',
            'organization_id' => 1,
            'owner_id' => 1,
            'created_at' => '2023-11-14T22:13:20+00:00',
            'is_blocked' => false,
            'is_deleted' => false,
            'metadata' => null,
            'created_by_source' => null,
            'domains' => [],
        ], 201);

        $website = $this->client($http)->websites->create([
            'name' => 'My Blog',
            'domain' => 'blog.example.com',
        ]);

        self::assertSame(7, $website->id);
        self::assertSame('My Blog', $website->name);
        self::assertSame([], $website->domains);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame('https://talk.hyvor.com/api/console/v1/websites', (string) $request->getUri());
        self::assertSame(
            ['name' => 'My Blog', 'domain' => 'blog.example.com'],
            json_decode((string) $request->getBody(), true),
        );
        self::assertSame('Bearer test-jwt-token', $request->getHeaderLine('Authorization'));
    }

    public function testCreateValidationErrorThrows(): void
    {
        $http = new FakeHttpClient();
        $http->queueResponse(new Response(422, [], json_encode([
            'message' => 'The given data was invalid.',
            'errors' => ['domain' => ['The domain has already been taken.']],
        ], JSON_THROW_ON_ERROR)));

        $client = $this->client($http);

        try {
            $client->websites->create(['name' => 'X', 'domain' => 'taken.com']);
            self::fail('Expected ValidationFailedException to be thrown.');
        } catch (ValidationFailedException $e) {
            self::assertSame(422, $e->statusCode);
            self::assertSame(['The domain has already been taken.'], $e->errors['domain']);
        }
    }

    public function testDelete(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->website(self::WEBSITE_ID)->delete();

        $request = $http->requests[0];
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame($this->baseUrl() . '/website', (string) $request->getUri());
        self::assertSame('Bearer test-jwt-token', $request->getHeaderLine('Authorization'));
    }

    public function testDeleteWithResourceApiKeyOverridesAuth(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->website(self::WEBSITE_ID, 'resource-api-key')->delete();

        $request = $http->requests[0];
        self::assertSame('Bearer resource-api-key', $request->getHeaderLine('Authorization'));
    }

    public function testProductUrlOverridesCloudInstanceDerivedUrl(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $factory = new Psr17Factory();
        $talk = new TalkClient(
            httpClient: $http,
            requestFactory: $factory,
            streamFactory: $factory,
            tokenProvider: new StaticTokenProvider('test-jwt-token'),
            productUrl: 'https://talk.example.com',
        );

        $talk->website(self::WEBSITE_ID)->delete();

        $request = $http->requests[0];
        self::assertSame(
            'https://talk.example.com/api/console/v1/' . self::WEBSITE_ID . '/website',
            (string) $request->getUri(),
        );
    }
}
