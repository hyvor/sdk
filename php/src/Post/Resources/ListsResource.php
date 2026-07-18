<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Post\Dto\List\SubscriberList;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->post->newsletter($newsletterId)->lists`
 */
final class ListsResource extends NewsletterScopedResource
{
    /**
     * POST /lists
     *
     * @param array{
     *     name: string,
     *     description?: string,
     * } $data name max length: 255.
     *
     * @throws HyvorApiException
     */
    public function create(array $data, ?RequestOptions $options = null): SubscriberList
    {
        $result = $this->request('POST', $this->path('/lists'), $data, $options);

        return $this->transport->denormalize($result, SubscriberList::class);
    }

    /**
     * PATCH /lists/{id}
     *
     * Every key is optional; keys left out of `$data` are left unchanged.
     *
     * @param array{
     *     name?: string,
     *     description?: string,
     * } $data name max length: 255.
     *
     * @throws HyvorApiException
     */
    public function update(int $id, array $data, ?RequestOptions $options = null): SubscriberList
    {
        $result = $this->request('PATCH', $this->path("/lists/{$id}"), $data, $options);

        return $this->transport->denormalize($result, SubscriberList::class);
    }

    /**
     * DELETE /lists/{id}
     *
     * @throws HyvorApiException
     */
    public function delete(int $id, ?RequestOptions $options = null): void
    {
        $this->request('DELETE', $this->path("/lists/{$id}"), null, $options);
    }
}
