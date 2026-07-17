<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Auth;

use Hyvor\Sdk\Auth\TokenProvider;
use Hyvor\Sdk\Exceptions\AuthenticationException;
use Hyvor\Sdk\Http\HttpResponse;
use Hyvor\Sdk\Tests\Support\FakeHttpClient;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

final class TokenProviderTest extends TestCase
{
    public function testExchangesCloudApiKeyForToken(): void
    {
        $http = new FakeHttpClient();
        $http->queueResponse(new HttpResponse(200, [], json_encode([
            'token' => 'short-lived-jwt',
            'expires_in' => 3600,
        ], JSON_THROW_ON_ERROR)));

        $provider = new TokenProvider('cloud-key', null, 'https://hyvor.com', $http, new NullLogger());

        self::assertSame('short-lived-jwt', $provider->getToken());
        self::assertSame('short-lived-jwt', $provider->getToken(), 'should be cached, not re-fetched');
        self::assertCount(1, $http->requests);
        self::assertSame('https://hyvor.com/api/cloud/token', $http->requests[0]->url);
    }

    public function testStaticJwtTokenIsUsedDirectly(): void
    {
        $http = new FakeHttpClient();
        $provider = new TokenProvider(null, 'static-jwt', 'https://hyvor.com', $http, new NullLogger());

        self::assertSame('static-jwt', $provider->getToken());
        self::assertCount(0, $http->requests);
    }

    public function testThrowsAuthenticationExceptionOnFailedExchange(): void
    {
        $http = new FakeHttpClient();
        $http->queueResponse(new HttpResponse(401, [], json_encode(['message' => 'Invalid API key'])));

        $provider = new TokenProvider('bad-key', null, 'https://hyvor.com', $http, new NullLogger());

        $this->expectException(AuthenticationException::class);
        $provider->getToken();
    }
}
