<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Http\Transport;
use Hyvor\Sdk\Post\Dto\Newsletter\Newsletter;
use Hyvor\Sdk\RequestOptions;

/**
 * Org-level access to newsletters, accessible via `$client->post->newsletters`.
 *
 * Requires org-level auth (a cloud API key or token provider), since it is
 * not scoped to a single newsletter.
 */
final class OrgNewslettersResource
{
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * POST /api/console/newsletters
     *
     * @param array{
     *     name: string,
     *     subdomain: string,
     *     autogenerate_subdomain_on_duplicate?: bool,
     *     metadata?: array<string, string>,
     * } $data
     * @throws HyvorApiException
     */
    public function create(array $data, ?RequestOptions $options = null): Newsletter
    {
        $data = $this->transport->request('POST', '/api/console/newsletters', $data, $options);

        return $this->transport->denormalize($data, Newsletter::class);
    }

}
