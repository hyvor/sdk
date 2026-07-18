<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Http\UploadedFile;
use Hyvor\Sdk\RequestOptions;
use Hyvor\Sdk\Talk\Dto\Media\ListMediaRequest;
use Hyvor\Sdk\Talk\Dto\Media\Media;
use Hyvor\Sdk\Talk\Dto\Media\UploadImageResponse;

/**
 * `$client->talk->website($websiteId)->media`
 */
final class MediaResource extends WebsiteScopedResource
{
    /**
     * GET /media
     *
     * @return Media[]
     * @throws HyvorApiException
     */
    public function list(?ListMediaRequest $request = null, ?RequestOptions $options = null): array
    {
        $body = $this->transport->normalize($request ?? new ListMediaRequest());
        $data = $this->request('GET', $this->path('/media'), $body, $options);

        return $this->transport->denormalizeList($data, Media::class);
    }

    /**
     * POST /media/image
     *
     * Max size 5 MB. Supported formats: jpg, jpeg, png, gif, svg, webp,
     * apng, avif.
     *
     * @throws HyvorApiException
     */
    public function uploadImage(UploadedFile $image, ?RequestOptions $options = null): UploadImageResponse
    {
        $data = $this->requestMultipart('POST', $this->path('/media/image'), [], ['image' => $image], $options);

        return $this->transport->denormalize($data, UploadImageResponse::class);
    }

    /**
     * DELETE /media/{id}
     *
     * @throws HyvorApiException
     */
    public function delete(int $id, ?RequestOptions $options = null): void
    {
        $this->request('DELETE', $this->path("/media/{$id}"), null, $options);
    }
}
