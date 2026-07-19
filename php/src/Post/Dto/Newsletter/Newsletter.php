<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\Newsletter;

/**
 * Represents a Hyvor Post newsletter and its full configuration, as returned
 * by `GET /newsletter` (see https://post.hyvor.com/docs/api-console#newsletter-object).
 *
 * Property names intentionally match the API's snake_case field names
 * one-to-one so the response can be denormalized without a name converter.
 */
final class Newsletter
{
    public function __construct(
        public readonly string $id,
        public readonly string $subdomain,
        public readonly int $created_at,
        public readonly string $name,

        public readonly ?string $address,
        public readonly ?string $unsubscribe_text,
        public readonly bool $branding,

        public readonly ?string $template_color_accent,
        public readonly ?string $template_color_accent_text,
        public readonly ?string $template_color_background,
        public readonly ?string $template_color_background_text,
        public readonly ?string $template_color_box,
        public readonly ?string $template_color_box_text,

        public readonly ?string $template_box_shadow,
        public readonly ?string $template_box_radius,
        public readonly ?string $template_box_border,

        public readonly ?string $template_font_family,
        public readonly ?string $template_font_size,
        public readonly ?string $template_font_weight,
        public readonly ?string $template_font_weight_heading,
        public readonly ?string $template_font_line_height,

        public readonly ?string $form_title,
        public readonly ?string $form_description,
        public readonly ?string $form_footer_text,
        public readonly ?string $form_button_text,
        public readonly ?string $form_success_message,

        public readonly ?int $form_width,
        public readonly ?string $form_custom_css,

        public readonly ?string $form_color_light_text,
        public readonly ?string $form_color_light_text_light,
        public readonly ?string $form_color_light_accent,
        public readonly ?string $form_color_light_accent_text,
        public readonly ?string $form_color_light_input,
        public readonly ?string $form_color_light_input_text,
        public readonly ?string $form_light_input_box_shadow,
        public readonly ?string $form_light_input_border,
        public readonly ?int $form_light_border_radius,

        public readonly ?string $form_color_dark_text,
        public readonly ?string $form_color_dark_text_light,
        public readonly ?string $form_color_dark_accent,
        public readonly ?string $form_color_dark_accent_text,
        public readonly ?string $form_color_dark_input,
        public readonly ?string $form_color_dark_input_text,
        public readonly ?string $form_dark_input_box_shadow,
        public readonly ?string $form_dark_input_border,
        public readonly ?int $form_dark_border_radius,

        public readonly FormColorPalette $form_default_color_palette,
        public readonly int $form_input_border_radius,

        /**
         * @var array<string, string>
         */
        public readonly array $metadata
    ) {
    }
}
