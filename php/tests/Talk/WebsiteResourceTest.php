<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Talk;

use Hyvor\Sdk\Exceptions\RateLimitException;
use Hyvor\Sdk\Exceptions\ServerErrorException;
use Hyvor\Sdk\Exceptions\ValidationFailedException;
use Hyvor\Sdk\Http\HttpResponse;
use Hyvor\Sdk\HyvorClient;
use Hyvor\Sdk\RequestOptions;
use Hyvor\Sdk\Talk\Dto\CreateWebsiteRequest;
use Hyvor\Sdk\Tests\Support\FakeHttpClient;
use PHPUnit\Framework\TestCase;

final class WebsiteResourceTest extends TestCase
{
    private function client(FakeHttpClient $httpClient, int $retryMaxAttempts = 3): HyvorClient
    {
        return new HyvorClient(
            jwtToken: 'test-jwt-token',
            httpClient: $httpClient,
            retryMaxAttempts: $retryMaxAttempts,
            connectTimeoutMs: 100,
            requestTimeoutMs: 100,
        );
    }

    public function testGetReturnsWebsite(): void
    {
        $http = new FakeHttpClient();
        $http->queueResponse(new HttpResponse(200, [], json_encode([
            'id' => 42,
            'name' => 'My Blog',
            'domain' => 'blog.example.com',
            'created_at' => '2026-01-01T00:00:00+00:00',
        ], JSON_THROW_ON_ERROR)));

        $client = $this->client($http);
        $website = $client->talk->website->get();

        self::assertSame(42, $website->id);
        self::assertSame('My Blog', $website->name);
        self::assertSame('blog.example.com', $website->domain);
        self::assertNotNull($website->createdAt);

        self::assertCount(1, $http->requests);
        self::assertSame('GET', $http->requests[0]->method);
        self::assertSame('https://hyvor.com/api/talk/website', $http->requests[0]->url);
        self::assertSame('Bearer test-jwt-token', $http->requests[0]->headers['Authorization']);
        self::assertStringStartsWith('hyvor/sdk-php/', $http->requests[0]->headers['User-Agent']);
    }

    public function testCreateSendsNameAndDomain(): void
    {
        $http = new FakeHttpClient();
        $http->queueResponse(new HttpResponse(201, [], json_encode([
            'id' => 7,
            'name' => 'New Site',
            'domain' => 'new.example.com',
        ], JSON_THROW_ON_ERROR)));

        $client = $this->client($http);
        $website = $client->talk->website->create(new CreateWebsiteRequest(
            name: 'New Site',
            domain: 'new.example.com',
        ));

        self::assertSame(7, $website->id);
        self::assertSame('New Site', $website->name);
        self::assertSame('new.example.com', $website->domain);

        $request = $http->requests[0];
        self::assertSame('POST', $request->method);
        self::assertSame('https://hyvor.com/api/talk/website', $request->url);
        self::assertSame(
            ['name' => 'New Site', 'domain' => 'new.example.com'],
            json_decode((string) $request->body, true),
        );
    }

    public function testCreateWithIdempotencyKey(): void
    {
        $http = new FakeHttpClient();
        $http->queueResponse(new HttpResponse(201, [], json_encode([
            'id' => 7, 'name' => 'New Site', 'domain' => 'new.example.com',
        ], JSON_THROW_ON_ERROR)));

        $client = $this->client($http);
        $client->talk->website->create(
            new CreateWebsiteRequest(name: 'New Site', domain: 'new.example.com'),
            new RequestOptions(idempotencyKey: 'idem-123'),
        );

        self::assertSame('idem-123', $http->requests[0]->headers['Idempotency-Key']);
    }

    public function testValidationErrorThrows(): void
    {
        $http = new FakeHttpClient();
        $http->queueResponse(new HttpResponse(422, [], json_encode([
            'message' => 'The given data was invalid.',
            'errors' => ['domain' => ['The domain field is required.']],
        ], JSON_THROW_ON_ERROR)));

        $client = $this->client($http);

        try {
            $client->talk->website->create(new CreateWebsiteRequest(name: 'X', domain: ''));
            self::fail('Expected ValidationFailedException to be thrown.');
        } catch (ValidationFailedException $e) {
            self::assertSame(422, $e->statusCode);
            self::assertSame(['The domain field is required.'], $e->errors['domain']);
        }
    }

    public function testRateLimitRetriesThenSucceeds(): void
    {
        $http = new FakeHttpClient();
        $http->queueResponse(new HttpResponse(429, ['retry-after' => '0'], json_encode(['message' => 'Too many requests'])));
        $http->queueResponse(new HttpResponse(200, [], json_encode([
            'id' => 1, 'name' => 'Site', 'domain' => 'site.example.com',
        ], JSON_THROW_ON_ERROR)));

        $client = $this->client($http, retryMaxAttempts: 2);
        $website = $client->talk->website->get();

        self::assertSame(1, $website->id);
        self::assertCount(2, $http->requests);
    }

    public function testRateLimitExhaustsRetriesAndThrows(): void
    {
        $http = new FakeHttpClient();
        $http->queueResponse(new HttpResponse(429, ['retry-after' => '0'], json_encode(['message' => 'Too many requests'])));
        $http->queueResponse(new HttpResponse(429, ['retry-after' => '0'], json_encode(['message' => 'Too many requests'])));

        $client = $this->client($http, retryMaxAttempts: 2);

        $this->expectException(RateLimitException::class);
        $client->talk->website->get();
    }

    public function testServerErrorThrows(): void
    {
        $http = new FakeHttpClient();
        $http->queueResponse(new HttpResponse(500, [], json_encode(['message' => 'Internal error'])));
        $http->queueResponse(new HttpResponse(500, [], json_encode(['message' => 'Internal error'])));

        $client = $this->client($http, retryMaxAttempts: 2);

        $this->expectException(ServerErrorException::class);
        $client->talk->website->get();
    }
}
