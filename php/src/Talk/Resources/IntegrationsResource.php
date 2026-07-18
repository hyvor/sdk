<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Resources;

use Hyvor\Sdk\Http\Transport;

/**
 * `$client->talk->website($websiteId)->integrations`
 */
final class IntegrationsResource extends WebsiteScopedResource
{
    public readonly SlackResource $slack;

    public function __construct(
        Transport $transport,
        int|string $websiteId,
        ?string $apiKey = null,
        array $headers = [],
    ) {
        parent::__construct($transport, $websiteId, $apiKey, $headers);
        $this->slack = new SlackResource($transport, $websiteId, $apiKey, $headers);
    }
}
