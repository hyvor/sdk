<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Post;

use Hyvor\Sdk\Testing\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\PostTestCase;

/**
 * `MediaUploadInput` only documents the non-file `folder` field - the spec
 * has no way to express the actual file part of a `multipart/form-data`
 * upload, so the generator (correctly) produced a plain JSON-body method
 * here instead of a real file upload. This test covers that as-generated
 * behavior; a real upload needs a hand-written override.
 */
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

        $media = $this->client($http)->newsletter(self::NEWSLETTER_ID)->media->upload(
            ['folder' => 'issue_images'],
        );

        self::assertSame('https://cdn.example.com/cat.png', $media->url);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/media', (string) $request->getUri());
        self::assertSame(['folder' => 'issue_images'], json_decode((string) $request->getBody(), true));
        self::assertSame('nl_42', $request->getHeaderLine('X-Newsletter-Id'));
    }
}
