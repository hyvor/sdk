<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Talk;

use Hyvor\Sdk\Http\UploadedFile;
use Hyvor\Sdk\Tests\Support\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\TalkTestCase;

final class MediaTest extends TalkTestCase
{
    public function testList(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [
            ['id' => 1, 'created_at' => 1700000000, 'comment_id' => 5, 'name' => 'cat.png', 'size' => 1024, 'url' => 'https://cdn.example.com/cat.png'],
        ]);

        $media = $this->client($http)->talk->website(self::WEBSITE_ID)->media->list();

        self::assertCount(1, $media);
        self::assertSame('cat.png', $media[0]->name);
        self::assertSame($this->baseUrl() . '/media', (string) $http->requests[0]->getUri());
    }

    public function testUploadImage(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, ['url' => 'https://cdn.example.com/cat.png']);

        $response = $this->client($http)->talk->website(self::WEBSITE_ID)->media->uploadImage(
            new UploadedFile('cat.png', 'binary-data', 'image/png'),
        );

        self::assertSame('https://cdn.example.com/cat.png', $response->url);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/media/image', (string) $request->getUri());
        self::assertStringStartsWith('multipart/form-data; boundary=', $request->getHeaderLine('Content-Type'));
        self::assertStringContainsString('filename="cat.png"', (string) $request->getBody());
    }

    public function testDelete(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->talk->website(self::WEBSITE_ID)->media->delete(1);

        $request = $http->requests[0];
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame($this->baseUrl() . '/media/1', (string) $request->getUri());
    }
}
