<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\Newsletter;

/**
 * Input for `NewsletterClient::update()` (`PATCH /newsletter`). Every
 * property is optional; fields left null are not sent to the API and are
 * left unchanged (see `Transport::normalize()`). Matches `Newsletter` except
 * `id` and `created_at`, which cannot be updated.
 */
final class UpdateNewsletterRequest
{
    public function __construct(
        public readonly ?string $name = null,

        public readonly ?string $address = null,
        public readonly ?string $unsubscribe_text = null,
        public readonly ?bool $branding = null,

        public readonly ?string $template_color_accent = null,
        public readonly ?string $template_color_accent_text = null,
        public readonly ?string $template_color_background = null,
        public readonly ?string $template_color_background_text = null,
        public readonly ?string $template_color_box = null,
        public readonly ?string $template_color_box_text = null,

        public readonly ?string $template_box_shadow = null,
        public readonly ?string $template_box_radius = null,
        public readonly ?string $template_box_border = null,

        public readonly ?string $template_font_family = null,
        public readonly ?string $template_font_size = null,
        public readonly ?string $template_font_weight = null,
        public readonly ?string $template_font_weight_heading = null,
        public readonly ?string $template_font_line_height = null,

        public readonly ?string $form_title = null,
        public readonly ?string $form_description = null,
        public readonly ?string $form_footer_text = null,
        public readonly ?string $form_button_text = null,
        public readonly ?string $form_success_message = null,

        public readonly ?int $form_width = null,
        public readonly ?string $form_custom_css = null,

        public readonly ?string $form_color_light_text = null,
        public readonly ?string $form_color_light_text_light = null,
        public readonly ?string $form_color_light_accent = null,
        public readonly ?string $form_color_light_accent_text = null,
        public readonly ?string $form_color_light_input = null,
        public readonly ?string $form_color_light_input_text = null,
        public readonly ?string $form_light_input_box_shadow = null,
        public readonly ?string $form_light_input_border = null,
        public readonly ?int $form_light_border_radius = null,

        public readonly ?string $form_color_dark_text = null,
        public readonly ?string $form_color_dark_text_light = null,
        public readonly ?string $form_color_dark_accent = null,
        public readonly ?string $form_color_dark_accent_text = null,
        public readonly ?string $form_color_dark_input = null,
        public readonly ?string $form_color_dark_input_text = null,
        public readonly ?string $form_dark_input_box_shadow = null,
        public readonly ?string $form_dark_input_border = null,
        public readonly ?int $form_dark_border_radius = null,

        public readonly ?FormColorPalette $form_default_color_palette = null,
        public readonly ?int $form_input_border_radius = null,
    ) {
    }
}
