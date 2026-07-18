<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Website;

/**
 * Input for `WebsiteClient::update()` (`PATCH /website`). Every property is
 * optional; fields left null are not sent to the API and are left
 * unchanged (see `Transport::normalize()`).
 */
final class UpdateWebsiteRequest
{
    public function __construct(
        public readonly ?string $name = null,

        public readonly ?AuthType $auth_type = null,
        public readonly ?AuthSsoType $auth_sso_type = null,
        public readonly ?string $sso_stateless_private_key = null,
        public readonly ?string $sso_stateless_login_url = null,
        public readonly ?bool $sso_stateless_is_keyless = null,
        public readonly ?string $sso_openid_issuer_url = null,
        public readonly ?string $sso_openid_client_id = null,
        public readonly ?string $sso_openid_client_secret = null,

        public readonly ?PremoderationStatus $premoderation_status = null,
        public readonly ?bool $is_realtime_on = null,
        public readonly ?RealtimeTyping $realtime_typing = null,
        public readonly ?bool $is_realtime_online_count_on = null,
        public readonly ?bool $is_realtime_users_on = null,
        public readonly ?bool $is_user_profile_on = null,
        public readonly ?bool $is_keyboard_navigation_on = null,
        public readonly ?bool $is_ip_collection_on = null,
        public readonly ?bool $is_guest_commenting_on = null,
        public readonly ?GuestCommentingEmail $guest_commenting_email = null,
        public readonly ?int $guest_commenting_delay = null,
        public readonly ?string $default_avatar = null,

        public readonly ?string $text_comment_box = null,
        public readonly ?string $text_no_comments = null,
        public readonly ?string $text_comment_button = null,
        public readonly ?string $text_reply_box = null,
        public readonly ?string $text_reply_button = null,
        public readonly ?string $text_reactions = null,
        public readonly ?string $text_ratings = null,
        public readonly ?string $text_comment_count_0 = null,
        public readonly ?string $text_comment_count_1 = null,
        public readonly ?string $text_comment_count_multi = null,

        public readonly ?TopWidget $top_widget = null,

        public readonly ?int $ui_box_shadow = null,
        public readonly ?int $ui_box_roundness = null,
        public readonly ?int $ui_box_border_size = null,
        public readonly ?int $ui_box_width = null,
        public readonly ?int $ui_button_roundness = null,
        public readonly ?string $ui_box_border_color = null,
        public readonly ?string $ui_custom_css = null,

        public readonly ?bool $is_ch_new_on = null,
        public readonly ?string $ch_new_color = null,
        public readonly ?int $ch_upvote_1_threshold = null,
        public readonly ?int $ch_upvote_2_threshold = null,
        public readonly ?string $ch_upvote_1_color = null,
        public readonly ?string $ch_upvote_2_color = null,

        public readonly ?bool $is_profile_pictures_on = null,

        public readonly ?DisplayNameType $display_name_type = null,
        public readonly ?int $comments_per_request = null,
        public readonly ?int $replies_per_request = null,
        public readonly ?int $nested_levels = null,
        public readonly ?DisplayRepliedToType $display_replied_to_type = null,
        public readonly ?int $comments_char_limit = null,
        public readonly ?int $comments_min_char_limit = null,

        public readonly ?bool $comments_editing_enabled = null,
        public readonly ?int $comments_editing_timeout = null,
        public readonly ?bool $comments_force_guest_commenting = null,

        public readonly ?bool $is_emoji_enabled = null,
        public readonly ?bool $is_images_enabled = null,
        public readonly ?bool $is_embed_enabled = null,
        public readonly ?bool $is_math_enabled = null,
        public readonly ?bool $is_spoiler_enabled = null,
        public readonly ?bool $is_code_blocks_enabled = null,
        public readonly ?bool $is_links_enabled = null,
        public readonly ?bool $is_gifs_enabled = null,
        public readonly ?bool $is_inline_styles_enabled = null,
        public readonly ?bool $is_mentions_enabled = null,
        public readonly ?bool $is_blockquotes_enabled = null,

        public readonly ?bool $is_spam_detection_on = null,
        public readonly ?SpamDetectionProvider $spam_detection_provider = null,
        public readonly ?SpamDetectionFortguardModel $spam_detection_fortguard_model = null,
        public readonly ?int $spam_detection_fortguard_content_score = null,
        /** @var string[]|null */
        public readonly ?array $spam_detection_fortguard_languages = null,
        public readonly ?bool $spam_detection_fortguard_languages_exclude = null,
        /** @var string[]|null */
        public readonly ?array $spam_detection_fortguard_countries = null,
        public readonly ?bool $spam_detection_fortguard_countries_exclude = null,
        /** @var string[]|null */
        public readonly ?array $spam_detection_fortguard_sentiments = null,

        public readonly ?VoteType $vote_type = null,
        public readonly ?bool $is_vote_viewing_on = null,
        public readonly ?bool $is_guest_voting_on = null,
        public readonly ?Sort $sort = null,
        public readonly ?string $language = null,
        public readonly ?string $note = null,
        public readonly ?int $close_after_days = null,

        public readonly ?string $color_text = null,
        public readonly ?string $color_background_text = null,
        public readonly ?string $color_accent = null,
        public readonly ?string $color_accent_text = null,
        public readonly ?string $color_box = null,
        public readonly ?string $color_box_text = null,
        public readonly ?string $color_box_text_light = null,
        public readonly ?string $color_input = null,

        public readonly ?string $color_dark_text = null,
        public readonly ?string $color_dark_background_text = null,
        public readonly ?string $color_dark_accent = null,
        public readonly ?string $color_dark_accent_text = null,
        public readonly ?string $color_dark_box = null,
        public readonly ?string $color_dark_box_text = null,
        public readonly ?string $color_dark_box_text_light = null,
        public readonly ?string $color_dark_input = null,

        public readonly ?ColorTheme $color_theme = null,
        public readonly ?string $ratings_color = null,

        public readonly ?string $reaction_1 = null,
        public readonly ?string $reaction_2 = null,
        public readonly ?string $reaction_3 = null,
        public readonly ?string $reaction_4 = null,
        public readonly ?string $reaction_5 = null,
        public readonly ?string $reaction_6 = null,

        public readonly ?bool $is_reaction_1_on = null,
        public readonly ?bool $is_reaction_2_on = null,
        public readonly ?bool $is_reaction_3_on = null,
        public readonly ?bool $is_reaction_4_on = null,
        public readonly ?bool $is_reaction_5_on = null,
        public readonly ?bool $is_reaction_6_on = null,

        public readonly ?string $reaction_1_text = null,
        public readonly ?string $reaction_2_text = null,
        public readonly ?string $reaction_3_text = null,
        public readonly ?string $reaction_4_text = null,
        public readonly ?string $reaction_5_text = null,
        public readonly ?string $reaction_6_text = null,

        public readonly ?ReactionDisplayType $reaction_display_type = null,

        public readonly ?bool $email_send_to_users = null,
        public readonly ?NotifChannel $notif_channel = null,
        public readonly ?string $email_report_frequency = null,
        public readonly ?string $email_company_name = null,
        public readonly ?string $email_company_logo_url = null,
        public readonly ?string $email_company_address = null,
        public readonly ?string $email_alternate_address = null,
        public readonly ?string $email_website_url = null,
        public readonly ?string $email_notification_sending_address = null,

        public readonly ?string $webhook_url = null,
        public readonly ?bool $webhook_on_create = null,
        public readonly ?bool $webhook_on_edit = null,
        public readonly ?bool $webhook_on_delete = null,

        public readonly ?int $mod_badge_id = null,
        public readonly ?string $mod_alias_name = null,
        public readonly ?string $mod_alias_picture_url = null,

        public readonly ?bool $email_report_daily = null,
        public readonly ?bool $email_report_weekly = null,
        public readonly ?bool $email_report_monthly = null,

        public readonly ?bool $highlight_new = null,
        public readonly ?string $highlight_new_color = null,
        public readonly ?int $highlight_upvote_threshold_1 = null,
        public readonly ?string $highlight_upvote_threshold_1_color = null,
        public readonly ?int $highlight_upvote_threshold_2 = null,
        public readonly ?string $highlight_upvote_threshold_2_color = null,

        public readonly ?bool $newsletter_enabled = null,
        public readonly ?string $newsletter_title = null,
        public readonly ?string $newsletter_description = null,
        public readonly ?string $newsletter_button_text = null,
        public readonly ?string $newsletter_success_message = null,
        public readonly ?string $newsletter_custom_css = null,
        public readonly ?int $newsletter_width = null,
        public readonly ?bool $newsletter_ask_commenters_subscribe = null,
        public readonly ?bool $newsletter_auto_subscribe_members = null,
        public readonly ?bool $newsletter_auto_subscribe_commenters = null,
        /** @var string[]|null */
        public readonly ?array $newsletter_sending_addresses = null,
    ) {
    }
}
