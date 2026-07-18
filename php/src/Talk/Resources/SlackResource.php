<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\RequestOptions;
use Hyvor\Sdk\Talk\Dto\Integration\InitSlackIntegrationResponse;
use Hyvor\Sdk\Talk\Dto\Integration\SetSlackChannelRequest;
use Hyvor\Sdk\Talk\Dto\Integration\SlackIntegrationStatus;

/**
 * `$client->talk->website($websiteId)->integrations->slack`
 */
final class SlackResource extends WebsiteScopedResource
{
    /**
     * GET /integrations/slack
     *
     * @throws HyvorApiException
     */
    public function status(?RequestOptions $options = null): SlackIntegrationStatus
    {
        $data = $this->request('GET', $this->path('/integrations/slack'), null, $options);

        return $this->transport->denormalize($data, SlackIntegrationStatus::class);
    }

    /**
     * POST /integrations/slack
     *
     * @throws HyvorApiException
     */
    public function init(?RequestOptions $options = null): InitSlackIntegrationResponse
    {
        $data = $this->request('POST', $this->path('/integrations/slack'), null, $options);

        return $this->transport->denormalize($data, InitSlackIntegrationResponse::class);
    }

    /**
     * POST /integrations/slack/channel
     *
     * @throws HyvorApiException
     */
    public function setChannel(SetSlackChannelRequest $request, ?RequestOptions $options = null): void
    {
        $body = $this->transport->normalize($request);
        $this->request('POST', $this->path('/integrations/slack/channel'), $body, $options);
    }

    /**
     * DELETE /integrations/slack
     *
     * @throws HyvorApiException
     */
    public function disconnect(?RequestOptions $options = null): void
    {
        $this->request('DELETE', $this->path('/integrations/slack'), null, $options);
    }
}
