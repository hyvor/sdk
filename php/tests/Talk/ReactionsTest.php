<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Talk;

use Hyvor\Sdk\Talk\Dto\Reaction\ListReactionsRequest;
use Hyvor\Sdk\Talk\Dto\Reaction\ReactionType;
use Hyvor\Sdk\Tests\Support\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\TalkTestCase;

final class ReactionsTest extends TalkTestCase
{
    public function testList(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [
            ['id' => 1, 'created_at' => 1700000000, 'page' => $this->samplePage(), 'user' => $this->sampleGuestUser(), 'type' => 'love'],
        ]);

        $reactions = $this->client($http)->talk->website(self::WEBSITE_ID)->reactions->list(
            new ListReactionsRequest(type: ReactionType::LOVE),
        );

        self::assertCount(1, $reactions);
        self::assertSame(ReactionType::LOVE, $reactions[0]->type);
        self::assertSame($this->baseUrl() . '/reactions', (string) $http->requests[0]->getUri());
        self::assertSame(['type' => 'love'], json_decode((string) $http->requests[0]->getBody(), true));
    }

    public function testDelete(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->talk->website(self::WEBSITE_ID)->reactions->delete(3);

        $request = $http->requests[0];
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame($this->baseUrl() . '/reaction/3', (string) $request->getUri());
    }
}
