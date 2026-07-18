<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Post\Dto\List\CreateListRequest;
use Hyvor\Sdk\Post\Dto\List\SubscriberList;
use Hyvor\Sdk\Post\Dto\List\UpdateListRequest;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->post->newsletter($newsletterId)->lists`
 */
final class ListsResource extends NewsletterScopedResource
{
    /**
     * POST /lists
     *
     * @throws HyvorApiException
     */
    public function create(CreateListRequest $request, ?RequestOptions $options = null): SubscriberList
    {
        $body = $this->transport->normalize($request);
        $data = $this->request('POST', $this->path('/lists'), $body, $options);

        return $this->transport->denormalize($data, SubscriberList::class);
    }

    /**
     * PATCH /lists/{id}
     *
     * @throws HyvorApiException
     */
    public function update(int $id, UpdateListRequest $request, ?RequestOptions $options = null): SubscriberList
    {
        $body = $this->transport->normalize($request);
        $data = $this->request('PATCH', $this->path("/lists/{$id}"), $body, $options);

        return $this->transport->denormalize($data, SubscriberList::class);
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
