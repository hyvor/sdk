<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Org;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Http\Transport;
use Hyvor\Sdk\Talk\Dto\Website\WebsiteObject;
use Hyvor\Sdk\RequestOptions;

/**
 * Org-level access to websites, accessible via `$client->talk->websites`.
 *
 * Requires org-level auth (a cloud API key or token provider), since it is
 * not scoped to a single website.
 */
final class OrgWebsitesResource
{
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * POST /api/console/v1/websites
     *
     * @param array{
     *     name: string,
     *     domain: string,
     *     metadata?: array<string, mixed>,
     *     start_trial?: bool,
     *     owner_user_id?: int|null,
     * } $data name max length: 60.
     * @throws HyvorApiException
     */
    public function create(array $data, ?RequestOptions $options = null): WebsiteObject
    {
        $result = $this->transport->request('POST', '/api/console/v1/websites', $data, $options);

        return $this->transport->denormalize($result, WebsiteObject::class);
    }
}
