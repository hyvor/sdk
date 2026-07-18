<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Http\Transport;
use Hyvor\Sdk\Post\Dto\Newsletter\FormColorPalette;
use Hyvor\Sdk\Post\Dto\Newsletter\Newsletter;
use Hyvor\Sdk\Post\Resources\ExportsResource;
use Hyvor\Sdk\Post\Resources\InvitesResource;
use Hyvor\Sdk\Post\Resources\IssuesResource;
use Hyvor\Sdk\Post\Resources\ListsResource;
use Hyvor\Sdk\Post\Resources\MediaResource;
use Hyvor\Sdk\Post\Resources\SendingProfilesResource;
use Hyvor\Sdk\Post\Resources\SubscriberMetadataDefinitionsResource;
use Hyvor\Sdk\Post\Resources\SubscribersResource;
use Hyvor\Sdk\Post\Resources\TemplatesResource;
use Hyvor\Sdk\Post\Resources\UsersResource;
use Hyvor\Sdk\RequestOptions;

/**
 * Resource-level access to a single newsletter, accessible via
 * `$client->post->newsletter($newsletterId)`.
 *
 * Authenticated either with the client's org-level auth (a cloud API key or
 * token provider, which must have access to this newsletter), or with a
 * resource-level API key scoped to this newsletter, passed as `$apiKey`.
 *
 * Unlike Talk's Console API, Post's Console API doesn't embed the
 * newsletter's ID in the URL. Instead, every request carries an
 * `X-Newsletter-Id` header, which is what lets an org-level cloud API key
 * (otherwise valid for every newsletter the org can access) resolve to this
 * one newsletter.
 */
final class NewsletterClient
{
    public readonly IssuesResource $issues;
    public readonly ListsResource $lists;
    public readonly SubscribersResource $subscribers;
    public readonly SubscriberMetadataDefinitionsResource $subscriberMetadataDefinitions;
    public readonly SendingProfilesResource $sendingProfiles;
    public readonly TemplatesResource $templates;
    public readonly UsersResource $users;
    public readonly InvitesResource $invites;
    public readonly MediaResource $media;
    public readonly ExportsResource $exports;

    /** @var array<string, string> */
    private readonly array $resourceHeaders;

    /**
     * @param array<string, string> $headers Default headers merged into
     *  every request made through this client and its sub-resources.
     */
    public function __construct(
        private readonly Transport $transport,
        private readonly int|string $newsletterId,
        private readonly ?string $apiKey = null,
        private readonly array $headers = [],
    ) {
        // caller-supplied headers can override X-Newsletter-Id if they need to.
        $this->resourceHeaders = ['X-Newsletter-Id' => (string) $newsletterId, ...$headers];

        $this->issues = new IssuesResource($transport, $apiKey, $this->resourceHeaders);
        $this->lists = new ListsResource($transport, $apiKey, $this->resourceHeaders);
        $this->subscribers = new SubscribersResource($transport, $apiKey, $this->resourceHeaders);
        $this->subscriberMetadataDefinitions = new SubscriberMetadataDefinitionsResource($transport, $apiKey, $this->resourceHeaders);
        $this->sendingProfiles = new SendingProfilesResource($transport, $apiKey, $this->resourceHeaders);
        $this->templates = new TemplatesResource($transport, $apiKey, $this->resourceHeaders);
        $this->users = new UsersResource($transport, $apiKey, $this->resourceHeaders);
        $this->invites = new InvitesResource($transport, $apiKey, $this->resourceHeaders);
        $this->media = new MediaResource($transport, $apiKey, $this->resourceHeaders);
        $this->exports = new ExportsResource($transport, $apiKey, $this->resourceHeaders);
    }

    private function path(string $suffix = ''): string
    {
        return "/api/console{$suffix}";
    }

    /**
     * GET /newsletter
     *
     * @throws HyvorApiException
     */
    public function get(?RequestOptions $options = null): Newsletter
    {
        $data = $this->transport->request(
            'GET',
            $this->path('/newsletter'),
            null,
            $options,
            $this->apiKey,
            $this->resourceHeaders,
        );

        return $this->transport->denormalize($data, Newsletter::class);
    }

    /**
     * PATCH /newsletter
     *
     * Every key is optional; keys left out of `$data` are left unchanged.
     * Matches `Newsletter` except `id` and `created_at`, which cannot be
     * updated.
     *
     * @param array{
     *     name?: string,
     *     address?: string,
     *     unsubscribe_text?: string,
     *     branding?: bool,
     *     template_color_accent?: string,
     *     template_color_accent_text?: string,
     *     template_color_background?: string,
     *     template_color_background_text?: string,
     *     template_color_box?: string,
     *     template_color_box_text?: string,
     *     template_box_shadow?: string,
     *     template_box_radius?: string,
     *     template_box_border?: string,
     *     template_font_family?: string,
     *     template_font_size?: string,
     *     template_font_weight?: string,
     *     template_font_weight_heading?: string,
     *     template_font_line_height?: string,
     *     form_title?: string,
     *     form_description?: string,
     *     form_footer_text?: string,
     *     form_button_text?: string,
     *     form_success_message?: string,
     *     form_width?: int,
     *     form_custom_css?: string,
     *     form_color_light_text?: string,
     *     form_color_light_text_light?: string,
     *     form_color_light_accent?: string,
     *     form_color_light_accent_text?: string,
     *     form_color_light_input?: string,
     *     form_color_light_input_text?: string,
     *     form_light_input_box_shadow?: string,
     *     form_light_input_border?: string,
     *     form_light_border_radius?: int,
     *     form_color_dark_text?: string,
     *     form_color_dark_text_light?: string,
     *     form_color_dark_accent?: string,
     *     form_color_dark_accent_text?: string,
     *     form_color_dark_input?: string,
     *     form_color_dark_input_text?: string,
     *     form_dark_input_box_shadow?: string,
     *     form_dark_input_border?: string,
     *     form_dark_border_radius?: int,
     *     form_default_color_palette?: FormColorPalette|string,
     *     form_input_border_radius?: int,
     * } $data
     * @throws HyvorApiException
     */
    public function update(array $data, ?RequestOptions $options = null): Newsletter
    {
        $result = $this->transport->request(
            'PATCH',
            $this->path('/newsletter'),
            $data,
            $options,
            $this->apiKey,
            $this->resourceHeaders,
        );

        return $this->transport->denormalize($result, Newsletter::class);
    }
}
