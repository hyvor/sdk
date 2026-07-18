<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Website;

/**
 * Represents a Hyvor Talk website and its full configuration, as returned by
 * `GET /website` (see https://talk.hyvor.com/docs/api-console#website-object).
 *
 * Property names intentionally match the API's snake_case field names
 * one-to-one so the response can be denormalized without a name converter.
 */
final class Website
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,

        public readonly AuthType $auth_type,
        public readonly ?AuthSsoType $auth_sso_type,
        public readonly ?string $sso_stateless_private_key,
        public readonly ?string $sso_stateless_login_url,
        public readonly bool $sso_stateless_is_keyless,
        public readonly ?string $sso_openid_issuer_url,
        public readonly ?string $sso_openid_client_id,
        public readonly ?string $sso_openid_client_secret,

        public readonly PremoderationStatus $premoderation_status,
        public readonly bool $is_realtime_on,
        public readonly RealtimeTyping $realtime_typing,
        public readonly bool $is_realtime_online_count_on,
        public readonly bool $is_realtime_users_on,
        public readonly bool $is_user_profile_on,
        public readonly bool $is_keyboard_navigation_on,
        public readonly bool $is_ip_collection_on,
        public readonly bool $is_guest_commenting_on,
        public readonly GuestCommentingEmail $guest_commenting_email,
        public readonly int $guest_commenting_delay,
        public readonly ?string $default_avatar,

        public readonly ?string $text_comment_box,
        public readonly ?string $text_no_comments,
        public readonly ?string $text_comment_button,
        public readonly ?string $text_reply_box,
        public readonly ?string $text_reply_button,
        public readonly ?string $text_reactions,
        public readonly ?string $text_ratings,
        public readonly ?string $text_comment_count_0,
        public readonly ?string $text_comment_count_1,
        public readonly ?string $text_comment_count_multi,

        public readonly TopWidget $top_widget,

        public readonly int $ui_box_shadow,
        public readonly int $ui_box_roundness,
        public readonly int $ui_box_border_size,
        public readonly ?int $ui_box_width,
        public readonly int $ui_button_roundness,
        public readonly string $ui_box_border_color,
        public readonly ?string $ui_custom_css,

        public readonly bool $is_ch_new_on,
        public readonly ?string $ch_new_color,
        public readonly ?int $ch_upvote_1_threshold,
        public readonly ?int $ch_upvote_2_threshold,
        public readonly ?string $ch_upvote_1_color,
        public readonly ?string $ch_upvote_2_color,

        public readonly bool $is_profile_pictures_on,

        public readonly DisplayNameType $display_name_type,
        public readonly int $comments_per_request,
        public readonly int $replies_per_request,
        public readonly int $nested_levels,
        public readonly DisplayRepliedToType $display_replied_to_type,
        public readonly int $comments_char_limit,
        public readonly int $comments_min_char_limit,

        public readonly bool $comments_editing_enabled,
        public readonly int $comments_editing_timeout,
        public readonly bool $comments_force_guest_commenting,

        public readonly bool $is_emoji_enabled,
        public readonly bool $is_images_enabled,
        public readonly bool $is_embed_enabled,
        public readonly bool $is_math_enabled,

        public readonly bool $is_spam_detection_on,
        public readonly SpamDetectionProvider $spam_detection_provider,
        public readonly ?int $spam_detection_fortguard_content_score,
        /** @var string[] */
        public readonly array $spam_detection_fortguard_languages,
        public readonly bool $spam_detection_fortguard_languages_exclude,
        /** @var string[] */
        public readonly array $spam_detection_fortguard_countries,
        public readonly bool $spam_detection_fortguard_countries_exclude,
        /** @var string[] */
        public readonly array $spam_detection_fortguard_sentiments,

        public readonly VoteType $vote_type,
        public readonly bool $is_vote_viewing_on,
        public readonly bool $is_guest_voting_on,
        public readonly Sort $sort,
        public readonly string $language,
        public readonly ?string $note,
        public readonly int $close_after_days,

        public readonly ?string $color_text,
        public readonly ?string $color_background_text,
        public readonly ?string $color_accent,
        public readonly ?string $color_accent_text,
        public readonly ?string $color_box,
        public readonly ?string $color_box_text,
        public readonly ?string $color_box_text_light,
        public readonly ?string $color_input,

        public readonly ?string $color_dark_text,
        public readonly ?string $color_dark_background_text,
        public readonly ?string $color_dark_accent,
        public readonly ?string $color_dark_accent_text,
        public readonly ?string $color_dark_box,
        public readonly ?string $color_dark_box_text,
        public readonly ?string $color_dark_box_text_light,
        public readonly ?string $color_dark_input,

        public readonly ColorTheme $color_theme,
        public readonly ?string $ratings_color,

        public readonly ?string $reaction_1,
        public readonly ?string $reaction_2,
        public readonly ?string $reaction_3,
        public readonly ?string $reaction_4,
        public readonly ?string $reaction_5,
        public readonly ?string $reaction_6,

        public readonly bool $is_reaction_1_on,
        public readonly bool $is_reaction_2_on,
        public readonly bool $is_reaction_3_on,
        public readonly bool $is_reaction_4_on,
        public readonly bool $is_reaction_5_on,
        public readonly bool $is_reaction_6_on,

        public readonly ?string $reaction_1_text,
        public readonly ?string $reaction_2_text,
        public readonly ?string $reaction_3_text,
        public readonly ?string $reaction_4_text,
        public readonly ?string $reaction_5_text,
        public readonly ?string $reaction_6_text,

        public readonly ReactionDisplayType $reaction_display_type,

        public readonly bool $email_send_to_users,
        public readonly NotifChannel $notif_channel,
        public readonly string $email_report_frequency,
        public readonly ?string $email_company_name,
        public readonly ?string $email_company_logo_url,
        public readonly ?string $email_company_address,
        public readonly ?string $email_alternate_address,
        public readonly ?string $email_website_url,
        public readonly ?string $email_notification_sending_address,

        public readonly ?string $encryption_key,
        public readonly ?string $data_api_key,
        public readonly bool $is_data_api_public,
        public readonly ?string $console_api_key,

        public readonly ?string $webhook_url,
        public readonly bool $webhook_on_create,
        public readonly bool $webhook_on_edit,
        public readonly bool $webhook_on_delete,
        public readonly ?string $webhook_secret,

        public readonly ?int $mod_badge_id,
        public readonly ?string $mod_alias_name,
        public readonly ?string $mod_alias_picture_url,

        public readonly bool $memberships_enabled,
        public readonly MembershipsCurrency $memberships_currency,
        public readonly ?int $memberships_yearly_discount,
        public readonly ?string $memberships_text_button,
        public readonly ?string $memberships_text_modal_title,
        public readonly ?string $memberships_text_modal_title_members,
        public readonly ?string $memberships_text_login_to_subscribe,
        public readonly ?string $memberships_text_subscribe_as,
        public readonly ?string $memberships_text_manage_subscription,
        public readonly ?string $memberships_text_payment_success,
        public readonly ?string $memberships_payment_success_url,
        public readonly bool $memberships_gated_allow_google,

        public readonly bool $email_report_daily,
        public readonly bool $email_report_weekly,
        public readonly bool $email_report_monthly,

        public readonly bool $highlight_new,
        public readonly ?string $highlight_new_color,
        public readonly ?int $highlight_upvote_threshold_1,
        public readonly ?string $highlight_upvote_threshold_1_color,
        public readonly ?int $highlight_upvote_threshold_2,
        public readonly ?string $highlight_upvote_threshold_2_color,
    ) {
    }
}
