<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Newsletter;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Post\Dto\Template;
use Hyvor\Sdk\Post\Newsletter;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->newsletter($newsletterId)->templates`
 */
final class TemplatesResource
{
    public function __construct(private readonly Newsletter $client)
    {
    }

    /**
     * GET /api/console/templates
     *
     * @throws HyvorApiException
     */
    public function get(?RequestOptions $options = null): Template
    {
        $result = $this->client->request('GET', $this->client->path('/api/console/templates'), null, $options);

        return $this->client->transport->denormalize($result, Template::class);
    }

    /**
     * PATCH /api/console/templates
     *
     * @param array{
     *     template?: string|null,
     * } $data
     *
     * @throws HyvorApiException
     */
    public function update(array $data, ?RequestOptions $options = null): Template
    {
        $result = $this->client->request('PATCH', $this->client->path('/api/console/templates'), $data, $options);

        return $this->client->transport->denormalize($result, Template::class);
    }

    /**
     * POST /api/console/templates/render
     *
     * @param array{
     *     template?: string|null,
     * } $data
     *
     * @return array<mixed>
     *
     * @throws HyvorApiException
     */
    public function preview(array $data, ?RequestOptions $options = null): array
    {
        return $this->client->request('POST', $this->client->path('/api/console/templates/render'), $data, $options);
    }
}
