<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Post;

use Hyvor\Sdk\Testing\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\PostTestCase;

final class TemplateTest extends PostTestCase
{
    public function testGet(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, ['template' => '<html>{{ content }}</html>']);

        $template = $this->client($http)->newsletter(self::NEWSLETTER_ID)->template->get();

        self::assertSame('<html>{{ content }}</html>', $template->template);
        self::assertSame($this->baseUrl() . '/templates', (string) $http->requests[0]->getUri());
    }

    public function testUpdate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, ['template' => '<html>new</html>']);

        $template = $this->client($http)->newsletter(self::NEWSLETTER_ID)->template->update(
            ['template' => '<html>new</html>'],
        );

        self::assertSame('<html>new</html>', $template->template);

        $request = $http->requests[0];
        self::assertSame('PATCH', $request->getMethod());
        self::assertSame($this->baseUrl() . '/templates', (string) $request->getUri());
        self::assertSame(['template' => '<html>new</html>'], json_decode((string) $request->getBody(), true));
    }

    /**
     * `/templates/render`'s response is an inline `{html}` shape (not a
     * named schema), so `preview()` returns the decoded body as a raw array
     * instead of a typed DTO.
     */
    public function testPreview(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, ['html' => '<html>rendered</html>']);

        $response = $this->client($http)->newsletter(self::NEWSLETTER_ID)->template->preview(
            ['template' => '<html>{{ content }}</html>'],
        );

        self::assertSame('<html>rendered</html>', $response['html']);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/templates/render', (string) $request->getUri());
    }
}
