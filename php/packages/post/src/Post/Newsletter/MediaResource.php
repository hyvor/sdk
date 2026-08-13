<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Newsletter;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Post\Dto\Media;
use Hyvor\Sdk\Post\Newsletter;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->newsletter($newsletterId)->media`
 */
final class MediaResource
{
    public function __construct(private readonly Newsletter $client)
    {
    }

    /**
     * POST /api/console/media
     *
     * @param array{
     *     folder: 'issue_images'|'newsletter_images'|'import'|'export',
     * } $data
     *
     * @throws HyvorApiException
     */
    public function upload(array $data, ?RequestOptions $options = null): Media
    {
        $result = $this->client->request('POST', $this->client->path('/api/console/media'), $data, $options);

        return $this->client->transport->denormalize($result, Media::class);
    }
}
