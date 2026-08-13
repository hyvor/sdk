<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Org;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Http\Transport;
use Hyvor\Sdk\Post\Dto\Newsletter;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->org->newsletters`
 */
final class NewslettersResource
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
     *     start_trial?: bool,
     * } $data
     *
     * @throws HyvorApiException
     */
    public function create(array $data, ?RequestOptions $options = null): Newsletter
    {
        $result = $this->transport->request('POST', '/api/console/newsletters', $data, $options);

        return $this->transport->denormalize($result, Newsletter::class);
    }
}
