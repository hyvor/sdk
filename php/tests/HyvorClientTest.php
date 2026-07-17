<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests;

use Hyvor\Sdk\HyvorClient;
use Hyvor\Sdk\Tests\Support\FakeHttpClient;
use PHPUnit\Framework\TestCase;

final class HyvorClientTest extends TestCase
{
    public function testThrowsWhenNoCredentialsProvided(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new HyvorClient();
    }

    public function testAcceptsJwtToken(): void
    {
        $client = new HyvorClient(jwtToken: 'abc', httpClient: new FakeHttpClient());
        self::assertInstanceOf(HyvorClient::class, $client);
    }

    public function testAcceptsCloudApiKey(): void
    {
        $client = new HyvorClient(cloudApiKey: 'abc', httpClient: new FakeHttpClient());
        self::assertInstanceOf(HyvorClient::class, $client);
    }
}
