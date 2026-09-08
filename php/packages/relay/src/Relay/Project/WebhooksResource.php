<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Relay\Project;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Relay\Dto\Webhook;
use Hyvor\Sdk\Relay\Dto\WebhookDelivery;
use Hyvor\Sdk\Relay\Project;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->project($projectId)->webhooks`
 */
final class WebhooksResource
{
    public function __construct(private readonly Project $client)
    {
    }

    /**
     * GET /api/console/webhooks
     *
     * @return Webhook[]
     *
     * @throws HyvorApiException
     */
    public function list(?RequestOptions $options = null): array
    {
        $result = $this->client->request('GET', $this->client->path('/api/console/webhooks'), null, $options);

        return $this->client->transport->denormalizeList($result, Webhook::class);
    }

    /**
     * POST /api/console/webhooks
     *
     * @param array{
     *     url: string,
     *     description: string,
     *     events: list<string>,
     * } $data
     *
     * @throws HyvorApiException
     */
    public function create(array $data, ?RequestOptions $options = null): Webhook
    {
        $result = $this->client->request('POST', $this->client->path('/api/console/webhooks'), $data, $options);

        return $this->client->transport->denormalize($result, Webhook::class);
    }

    /**
     * DELETE /api/console/webhooks/{id}
     *
     * @throws HyvorApiException
     */
    public function delete(int $id, ?RequestOptions $options = null): void
    {
        $this->client->request('DELETE', $this->client->path('/api/console/webhooks/' . $id), null, $options);
    }

    /**
     * PATCH /api/console/webhooks/{id}
     *
     * @param array{
     *     url: string,
     *     description: string,
     *     events: list<string>,
     * } $data
     *
     * @throws HyvorApiException
     */
    public function update(int $id, array $data, ?RequestOptions $options = null): Webhook
    {
        $result = $this->client->request('PATCH', $this->client->path('/api/console/webhooks/' . $id), $data, $options);

        return $this->client->transport->denormalize($result, Webhook::class);
    }

    /**
     * GET /api/console/webhooks/deliveries
     *
     * @return WebhookDelivery[]
     *
     * @throws HyvorApiException
     */
    public function listdeliveries(?RequestOptions $options = null): array
    {
        $result = $this->client->request('GET', $this->client->path('/api/console/webhooks/deliveries'), null, $options);

        return $this->client->transport->denormalizeList($result, WebhookDelivery::class);
    }
}
