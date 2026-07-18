<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Http\UploadedFile;
use Hyvor\Sdk\Post\Dto\Media\Media;
use Hyvor\Sdk\Post\Dto\Media\UploadMediaFolder;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->post->newsletter($newsletterId)->media`
 */
final class MediaResource extends NewsletterScopedResource
{
    /**
     * POST /media
     *
     * Max size 10 MB. Supported formats: jpg, jpeg, png, gif, webp.
     *
     * @throws HyvorApiException
     */
    public function upload(UploadedFile $file, UploadMediaFolder $folder, ?RequestOptions $options = null): Media
    {
        $data = $this->requestMultipart(
            'POST',
            $this->path('/media'),
            ['folder' => $folder->value],
            ['file' => $file],
            $options,
        );

        return $this->transport->denormalize($data, Media::class);
    }
}
