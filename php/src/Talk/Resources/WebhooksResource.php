<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\RequestOptions;
use Hyvor\Sdk\Talk\Dto\Webhook\CreateWebhookRequest;
use Hyvor\Sdk\Talk\Dto\Webhook\ListWebhookDeliveriesRequest;
use Hyvor\Sdk\Talk\Dto\Webhook\UpdateWebhookRequest;
use Hyvor\Sdk\Talk\Dto\Webhook\WebhookConfiguration;
use Hyvor\Sdk\Talk\Dto\Webhook\WebhookDelivery;

/**
 * `$client->talk->website($websiteId)->webhooks`
 */
final class WebhooksResource extends WebsiteScopedResource
{
    /**
     * GET /webhooks
     *
     * @return WebhookConfiguration[]
     * @throws HyvorApiException
     */
    public function list(?RequestOptions $options = null): array
    {
        $data = $this->request('GET', $this->path('/webhooks'), null, $options);

        return $this->transport->denormalizeList($data, WebhookConfiguration::class);
    }

    /**
     * POST /webhook
     *
     * @throws HyvorApiException
     */
    public function create(CreateWebhookRequest $request, ?RequestOptions $options = null): WebhookConfiguration
    {
        $body = $this->transport->normalize($request);
        $data = $this->request('POST', $this->path('/webhook'), $body, $options);

        return $this->transport->denormalize($data, WebhookConfiguration::class);
    }

    /**
     * PATCH /webhook/{id}
     *
     * @throws HyvorApiException
     */
    public function update(int $id, UpdateWebhookRequest $request, ?RequestOptions $options = null): WebhookConfiguration
    {
        $body = $this->transport->normalize($request);
        $data = $this->request('PATCH', $this->path("/webhook/{$id}"), $body, $options);

        return $this->transport->denormalize($data, WebhookConfiguration::class);
    }

    /**
     * DELETE /webhook/{id}
     *
     * @throws HyvorApiException
     */
    public function delete(int $id, ?RequestOptions $options = null): void
    {
        $this->request('DELETE', $this->path("/webhook/{$id}"), null, $options);
    }

    /**
     * GET /webhook/deliveries
     *
     * @return WebhookDelivery[]
     * @throws HyvorApiException
     */
    public function deliveries(?ListWebhookDeliveriesRequest $request = null, ?RequestOptions $options = null): array
    {
        $body = $this->transport->normalize($request ?? new ListWebhookDeliveriesRequest());
        $data = $this->request('GET', $this->path('/webhook/deliveries'), $body, $options);

        return $this->transport->denormalizeList($data, WebhookDelivery::class);
    }
}
