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
final readonly class Newsletter
{
    public function __construct(
        public int $id,
        public string $subdomain,
        public int $created_at,
        public string $name,

        public ?string $address,
        public ?string $unsubscribe_text,
        public bool $branding,

        public ?string $template_color_accent,
        public ?string $template_color_accent_text,
        public ?string $template_color_background,
        public ?string $template_color_background_text,
        public ?string $template_color_box,
        public ?string $template_color_box_text,

        public ?string $template_box_shadow,
        public ?string $template_box_radius,
        public ?string $template_box_border,

        public ?string $template_font_family,
        public ?string $template_font_size,
        public ?string $template_font_weight,
        public ?string $template_font_weight_heading,
        public ?string $template_font_line_height,

        public ?string $form_title,
        public ?string $form_description,
        public ?string $form_footer_text,
        public ?string $form_button_text,
        public ?string $form_success_message,

        public ?int $form_width,
        public ?string $form_custom_css,

        public ?string $form_color_light_text,
        public ?string $form_color_light_text_light,
        public ?string $form_color_light_accent,
        public ?string $form_color_light_accent_text,
        public ?string $form_color_light_input,
        public ?string $form_color_light_input_text,
        public ?string $form_light_input_box_shadow,
        public ?string $form_light_input_border,
        public ?int $form_light_border_radius,

        public ?string $form_color_dark_text,
        public ?string $form_color_dark_text_light,
        public ?string $form_color_dark_accent,
        public ?string $form_color_dark_accent_text,
        public ?string $form_color_dark_input,
        public ?string $form_color_dark_input_text,
        public ?string $form_dark_input_box_shadow,
        public ?string $form_dark_input_border,
        public ?int $form_dark_border_radius,

        public FormColorPalette $form_default_color_palette,
        public int $form_input_border_radius,

        /**
         * @var array<string, string>
         */
        public array $metadata
    ) {
    }
}
