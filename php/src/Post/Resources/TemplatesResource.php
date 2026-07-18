<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Post\Dto\Template\RenderTemplateResponse;
use Hyvor\Sdk\Post\Dto\Template\Template;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->post->newsletter($newsletterId)->templates`
 */
final class TemplatesResource extends NewsletterScopedResource
{
    /**
     * GET /templates
     *
     * @throws HyvorApiException
     */
    public function get(?RequestOptions $options = null): Template
    {
        $data = $this->request('GET', $this->path('/templates'), null, $options);

        return $this->transport->denormalize($data, Template::class);
    }

    /**
     * PATCH /templates
     *
     * @param array{
     *     template?: string,
     * } $data
     * @throws HyvorApiException
     */
    public function update(array $data, ?RequestOptions $options = null): Template
    {
        $result = $this->request('PATCH', $this->path('/templates'), $data, $options);

        return $this->transport->denormalize($result, Template::class);
    }

    /**
     * POST /templates/render
     *
     * @param array{
     *     template?: string,
     * } $data
     * @throws HyvorApiException
     */
    public function render(array $data = [], ?RequestOptions $options = null): RenderTemplateResponse
    {
        $result = $this->request('POST', $this->path('/templates/render'), $data, $options);

        return $this->transport->denormalize($result, RenderTemplateResponse::class);
    }
}
