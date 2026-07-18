<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Post\Dto\Export\SubscriberExport;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->post->newsletter($newsletterId)->exports`
 */
final class ExportsResource extends NewsletterScopedResource
{
    /**
     * GET /export
     *
     * @return SubscriberExport[]
     * @throws HyvorApiException
     */
    public function list(?RequestOptions $options = null): array
    {
        $data = $this->request('GET', $this->path('/export'), null, $options);

        return $this->transport->denormalizeList($data, SubscriberExport::class);
    }

    /**
     * POST /export
     *
     * Exporting is asynchronous; check `SubscriberExport::$status` for
     * completion.
     *
     * @throws HyvorApiException
     */
    public function create(?RequestOptions $options = null): SubscriberExport
    {
        $data = $this->request('POST', $this->path('/export'), null, $options);

        return $this->transport->denormalize($data, SubscriberExport::class);
    }
}
