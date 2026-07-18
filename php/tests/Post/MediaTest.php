<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Post;

use Hyvor\Sdk\Http\UploadedFile;
use Hyvor\Sdk\Post\Dto\Media\UploadMediaFolder;
use Hyvor\Sdk\Tests\Support\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\PostTestCase;

final class MediaTest extends PostTestCase
{
    public function testUpload(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [
            'id' => 1,
            'created_at' => 1700000000,
            'folder' => 'issue_images',
            'url' => 'https://cdn.example.com/cat.png',
            'size' => 1024,
            'extension' => 'png',
        ], 201);

        $media = $this->client($http)->post->newsletter(self::NEWSLETTER_ID)->media->upload(
            new UploadedFile('cat.png', 'binary-data', 'image/png'),
            UploadMediaFolder::ISSUE_IMAGES,
        );

        self::assertSame('https://cdn.example.com/cat.png', $media->url);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/media', (string) $request->getUri());
        self::assertStringStartsWith('multipart/form-data; boundary=', $request->getHeaderLine('Content-Type'));
        self::assertStringContainsString('filename="cat.png"', (string) $request->getBody());
        self::assertSame('nl_42', $request->getHeaderLine('X-Newsletter-Id'));
    }
}
