<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Talk;

use Hyvor\Sdk\Talk\Dto\Page\CreatePageRequest;
use Hyvor\Sdk\Talk\Dto\Page\ListPagesRequest;
use Hyvor\Sdk\Talk\Dto\Page\PageSort;
use Hyvor\Sdk\Talk\Dto\Page\ResetPageDataRequest;
use Hyvor\Sdk\Talk\Dto\Page\UpdatePageRequest;
use Hyvor\Sdk\Tests\Support\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\TalkTestCase;

final class PagesTest extends TalkTestCase
{
    public function testList(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [$this->samplePage()]);

        $pages = $this->client($http)->talk->website(self::WEBSITE_ID)->pages->list(
            new ListPagesRequest(sort: PageSort::MOST_COMMENTED),
        );

        self::assertCount(1, $pages);
        self::assertSame($this->baseUrl() . '/pages', (string) $http->requests[0]->getUri());
        self::assertSame(['sort' => 'most_commented'], json_decode((string) $http->requests[0]->getBody(), true));
    }

    public function testCreate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->samplePage());

        $page = $this->client($http)->talk->website(self::WEBSITE_ID)->pages->create(
            new CreatePageRequest(identifier: 'post-1', url: 'https://example.com/post-1'),
        );

        self::assertSame('post-1', $page->identifier);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/page', (string) $request->getUri());
    }

    public function testUpdateByInternalId(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->samplePage(['is_closed' => true]));

        $page = $this->client($http)->talk->website(self::WEBSITE_ID)->pages->update(
            1,
            new UpdatePageRequest(is_closed: true),
        );

        self::assertTrue($page->is_closed);

        $request = $http->requests[0];
        self::assertSame($this->baseUrl() . '/page/1', (string) $request->getUri());
        self::assertSame('', $request->getHeaderLine('X-ID-Type'));
    }

    public function testUpdateByIdentifierSetsIdTypeHeader(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->samplePage());

        $this->client($http)->talk->website(self::WEBSITE_ID)->pages->update(
            'post-1',
            new UpdatePageRequest(is_closed: true),
            byIdentifier: true,
        );

        $request = $http->requests[0];
        self::assertSame($this->baseUrl() . '/page/post-1', (string) $request->getUri());
        self::assertSame('page_id', $request->getHeaderLine('X-ID-Type'));
    }

    public function testReset(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->samplePage());

        $this->client($http)->talk->website(self::WEBSITE_ID)->pages->reset(
            1,
            new ResetPageDataRequest(comments: true),
        );

        $request = $http->requests[0];
        self::assertSame($this->baseUrl() . '/page/1/reset', (string) $request->getUri());
        self::assertSame(
            ['comments' => true, 'reactions' => false, 'ratings' => false],
            json_decode((string) $request->getBody(), true),
        );
    }

    public function testMove(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->talk->website(self::WEBSITE_ID)->pages->move(1, 2);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/page/1/move', (string) $request->getUri());
        self::assertSame(['to_page_id' => 2], json_decode((string) $request->getBody(), true));
    }

    public function testDelete(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->talk->website(self::WEBSITE_ID)->pages->delete(1);

        $request = $http->requests[0];
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame($this->baseUrl() . '/page/1', (string) $request->getUri());
    }
}
