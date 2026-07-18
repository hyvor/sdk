<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Post\Dto\Template\RenderTemplateRequest;
use Hyvor\Sdk\Post\Dto\Template\RenderTemplateResponse;
use Hyvor\Sdk\Post\Dto\Template\Template;
use Hyvor\Sdk\Post\Dto\Template\UpdateTemplateRequest;
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
     * @throws HyvorApiException
     */
    public function update(UpdateTemplateRequest $request, ?RequestOptions $options = null): Template
    {
        $body = $this->transport->normalize($request);
        $data = $this->request('PATCH', $this->path('/templates'), $body, $options);

        return $this->transport->denormalize($data, Template::class);
    }

    /**
     * POST /templates/render
     *
     * @throws HyvorApiException
     */
    public function render(?RenderTemplateRequest $request = null, ?RequestOptions $options = null): RenderTemplateResponse
    {
        $body = $this->transport->normalize($request ?? new RenderTemplateRequest());
        $data = $this->request('POST', $this->path('/templates/render'), $body, $options);

        return $this->transport->denormalize($data, RenderTemplateResponse::class);
    }
}
