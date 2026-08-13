<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Http\Transport;
use Hyvor\Sdk\Post\Dto\Newsletter as DtoNewsletter;
use Hyvor\Sdk\Post\Newsletter\ApiKeyResource;
use Hyvor\Sdk\Post\Newsletter\ExportResource;
use Hyvor\Sdk\Post\Newsletter\ImportResource;
use Hyvor\Sdk\Post\Newsletter\IssueResource;
use Hyvor\Sdk\Post\Newsletter\ListResource;
use Hyvor\Sdk\Post\Newsletter\MediaResource;
use Hyvor\Sdk\Post\Newsletter\SendingProfileResource;
use Hyvor\Sdk\Post\Newsletter\SubscriberMetadataResource;
use Hyvor\Sdk\Post\Newsletter\SubscriberResource;
use Hyvor\Sdk\Post\Newsletter\TemplateResource;
use Hyvor\Sdk\Post\Newsletter\UserResource;
use Hyvor\Sdk\RequestOptions;

/**
 * Resource-level access to a single newsletter, accessible via
 * `$client->newsletter($newsletterId)`.
 *
 * Authenticated either with the client's org-level auth (a cloud API
 * key or token provider, which must have access to this newsletter), or
 * with a resource-level API key, passed as `$apiKey`.
 */
final class Newsletter
{
    public readonly ApiKeyResource $api_key;
    public readonly ExportResource $export;
    public readonly ImportResource $import;
    public readonly IssueResource $issue;
    public readonly ListResource $list;
    public readonly MediaResource $media;
    public readonly SendingProfileResource $sending_profile;
    public readonly SubscriberResource $subscriber;
    public readonly SubscriberMetadataResource $subscriber_metadata;
    public readonly TemplateResource $template;
    public readonly UserResource $user;
    /** @var array<string, string> */
    private readonly array $resourceHeaders;

    /**
     * @param array<string, string> $headers Default headers merged into
     *  every request made through this client and its sub-resources.
     */
    public function __construct(
        public readonly Transport $transport,
        private readonly int|string $newsletterId,
        private readonly ?string $apiKey = null,
        private readonly array $headers = [],
    ) {
        $this->resourceHeaders = ['X-Newsletter-Id' => (string) $newsletterId, ...$headers];

        $this->api_key = new ApiKeyResource($this);
        $this->export = new ExportResource($this);
        $this->import = new ImportResource($this);
        $this->issue = new IssueResource($this);
        $this->list = new ListResource($this);
        $this->media = new MediaResource($this);
        $this->sending_profile = new SendingProfileResource($this);
        $this->subscriber = new SubscriberResource($this);
        $this->subscriber_metadata = new SubscriberMetadataResource($this);
        $this->template = new TemplateResource($this);
        $this->user = new UserResource($this);
    }

    public function path(string $suffix = ''): string
    {
        return $suffix;
    }

    /**
     * @param array<mixed>|null $jsonBody
     * @return array<mixed>
     *
     * @throws HyvorApiException
     */
    public function request(
        string $method,
        string $path,
        ?array $jsonBody = null,
        ?RequestOptions $options = null,
    ): array {
        return $this->transport->request($method, $path, $jsonBody, $options, $this->apiKey, $this->resourceHeaders);
    }

    /**
     * GET /api/console/newsletter
     *
     * @throws HyvorApiException
     */
    public function get(?RequestOptions $options = null): DtoNewsletter
    {
        $result = $this->request('GET', $this->path('/api/console/newsletter'), null, $options);

        return $this->transport->denormalize($result, DtoNewsletter::class);
    }

    /**
     * DELETE /api/console/newsletter
     *
     * @throws HyvorApiException
     */
    public function delete(?RequestOptions $options = null): void
    {
        $this->request('DELETE', $this->path('/api/console/newsletter'), null, $options);
    }

    /**
     * PATCH /api/console/newsletter
     *
     * @param array{
     *     name: string,
     *     subdomain: string,
     *     setProperties?: list<string>|array<string, string>,
     *     id: int,
     *     created_at: int,
     *     language_code?: string|null,
     *     is_rtl: bool,
     *     metadata: array<string, string>,
     *     address?: string|null,
     *     unsubscribe_text?: string|null,
     *     branding?: bool,
     *     template_color_accent?: string|null,
     *     template_color_accent_text?: string|null,
     *     template_color_background?: string|null,
     *     template_color_background_text?: string|null,
     *     template_color_box?: string|null,
     *     template_color_box_text?: string|null,
     *     template_box_shadow?: string|null,
     *     template_box_radius?: string|null,
     *     template_box_border?: string|null,
     *     template_font_family?: string|null,
     *     template_font_size?: string|null,
     *     template_font_weight?: string|null,
     *     template_font_weight_heading?: string|null,
     *     template_font_line_height?: string|null,
     *     form_title?: string|null,
     *     form_description?: string|null,
     *     form_footer_text?: string|null,
     *     form_button_text?: string|null,
     *     form_success_message?: string|null,
     *     form_width?: int|null,
     *     form_custom_css?: string|null,
     *     form_color_light_text?: string|null,
     *     form_color_light_text_light?: string|null,
     *     form_color_light_accent?: string|null,
     *     form_color_light_accent_text?: string|null,
     *     form_color_light_input?: string|null,
     *     form_color_light_input_text?: string|null,
     *     form_light_input_box_shadow?: string|null,
     *     form_light_input_border?: string|null,
     *     form_light_border_radius?: int|null,
     *     form_color_dark_text?: string|null,
     *     form_color_dark_text_light?: string|null,
     *     form_color_dark_accent?: string|null,
     *     form_color_dark_accent_text?: string|null,
     *     form_color_dark_input?: string|null,
     *     form_color_dark_input_text?: string|null,
     *     form_dark_input_box_shadow?: string|null,
     *     form_dark_input_border?: string|null,
     *     form_dark_border_radius?: int|null,
     *     form_default_color_palette?: 'light'|'dark'|'os',
     *     form_input_border_radius?: int,
     * } $data
     *
     * @throws HyvorApiException
     */
    public function update(array $data, ?RequestOptions $options = null): DtoNewsletter
    {
        $result = $this->request('PATCH', $this->path('/api/console/newsletter'), $data, $options);

        return $this->transport->denormalize($result, DtoNewsletter::class);
    }
}
