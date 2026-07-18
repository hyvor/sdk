<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Post\Dto\Subscriber\BulkUpdateSubscribersRequest;
use Hyvor\Sdk\Post\Dto\Subscriber\BulkUpdateSubscribersResponse;
use Hyvor\Sdk\Post\Dto\Subscriber\CreateOrUpdateSubscriberRequest;
use Hyvor\Sdk\Post\Dto\Subscriber\ListSubscribersRequest;
use Hyvor\Sdk\Post\Dto\Subscriber\Subscriber;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->post->newsletter($newsletterId)->subscribers`
 */
final class SubscribersResource extends NewsletterScopedResource
{
    /**
     * GET /subscribers
     *
     * @return Subscriber[]
     * @throws HyvorApiException
     */
    public function list(?ListSubscribersRequest $request = null, ?RequestOptions $options = null): array
    {
        $body = $this->transport->normalize($request ?? new ListSubscribersRequest());
        $data = $this->request('GET', $this->path('/subscribers'), $body, $options);

        return $this->transport->denormalizeList($data, Subscriber::class);
    }

    /**
     * GET /subscribers/email/{email}
     *
     * @throws HyvorApiException Including `NotFoundException` if no
     *  subscriber has this email.
     */
    public function getByEmail(string $email, ?RequestOptions $options = null): Subscriber
    {
        $data = $this->request('GET', $this->path('/subscribers/email/' . rawurlencode($email)), null, $options);

        return $this->transport->denormalize($data, Subscriber::class);
    }

    /**
     * POST /subscribers
     *
     * Creates a new subscriber, or updates the existing one if a subscriber
     * with the given email already exists.
     *
     * @throws HyvorApiException
     */
    public function createOrUpdate(CreateOrUpdateSubscriberRequest $request, ?RequestOptions $options = null): Subscriber
    {
        $body = $this->transport->normalize($request);
        $data = $this->request('POST', $this->path('/subscribers'), $body, $options);

        return $this->transport->denormalize($data, Subscriber::class);
    }

    /**
     * DELETE /subscribers/{id}
     *
     * @throws HyvorApiException
     */
    public function delete(int $id, ?RequestOptions $options = null): void
    {
        $this->request('DELETE', $this->path("/subscribers/{$id}"), null, $options);
    }

    /**
     * POST /subscribers/bulk
     *
     * @throws HyvorApiException
     */
    public function bulkUpdate(
        BulkUpdateSubscribersRequest $request,
        ?RequestOptions $options = null,
    ): BulkUpdateSubscribersResponse {
        $body = $this->transport->normalize($request);
        $data = $this->request('POST', $this->path('/subscribers/bulk'), $body, $options);

        return $this->transport->denormalize($data, BulkUpdateSubscribersResponse::class);
    }
}
