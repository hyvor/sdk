<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Website;

/**
 * Represents a Hyvor Talk website and its full configuration, as returned by
 * `GET /website` (see https://talk.hyvor.com/docs/api-console#website-object).
 */
final class Website
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,

        public readonly AuthType $authType,
        public readonly ?AuthSsoType $authSsoType,
        public readonly ?string $ssoStatelessPrivateKey,
        public readonly ?string $ssoStatelessLoginUrl,
        public readonly bool $ssoStatelessIsKeyless,
        public readonly ?string $ssoOpenidIssuerUrl,
        public readonly ?string $ssoOpenidClientId,
        public readonly ?string $ssoOpenidClientSecret,

        public readonly PremoderationStatus $premoderationStatus,
        public readonly bool $isRealtimeOn,
        public readonly RealtimeTyping $realtimeTyping,
        public readonly bool $isRealtimeOnlineCountOn,
        public readonly bool $isRealtimeUsersOn,
        public readonly bool $isUserProfileOn,
        public readonly bool $isKeyboardNavigationOn,
        public readonly bool $isIpCollectionOn,
        public readonly bool $isGuestCommentingOn,
        public readonly GuestCommentingEmail $guestCommentingEmail,
        public readonly int $guestCommentingDelay,
        public readonly ?string $defaultAvatar,

        public readonly ?string $textCommentBox,
        public readonly ?string $textNoComments,
        public readonly ?string $textCommentButton,
        public readonly ?string $textReplyBox,
        public readonly ?string $textReplyButton,
        public readonly ?string $textReactions,
        public readonly ?string $textRatings,
        public readonly ?string $textCommentCount0,
        public readonly ?string $textCommentCount1,
        public readonly ?string $textCommentCountMulti,

        public readonly TopWidget $topWidget,

        public readonly int $uiBoxShadow,
        public readonly int $uiBoxRoundness,
        public readonly int $uiBoxBorderSize,
        public readonly ?int $uiBoxWidth,
        public readonly int $uiButtonRoundness,
        public readonly string $uiBoxBorderColor,
        public readonly ?string $uiCustomCss,

        public readonly bool $isChNewOn,
        public readonly ?string $chNewColor,
        public readonly ?int $chUpvote1Threshold,
        public readonly ?int $chUpvote2Threshold,
        public readonly ?string $chUpvote1Color,
        public readonly ?string $chUpvote2Color,

        public readonly bool $isProfilePicturesOn,

        public readonly DisplayNameType $displayNameType,
        public readonly int $commentsPerRequest,
        public readonly int $repliesPerRequest,
        public readonly int $nestedLevels,
        public readonly DisplayRepliedToType $displayRepliedToType,
        public readonly int $commentsCharLimit,
        public readonly int $commentsMinCharLimit,

        public readonly bool $commentsEditingEnabled,
        public readonly int $commentsEditingTimeout,
        public readonly bool $commentsForceGuestCommenting,

        public readonly bool $isEmojiEnabled,
        public readonly bool $isImagesEnabled,
        public readonly bool $isEmbedEnabled,
        public readonly bool $isMathEnabled,

        public readonly bool $isSpamDetectionOn,
        public readonly SpamDetectionProvider $spamDetectionProvider,
        public readonly ?int $spamDetectionFortguardContentScore,
        /** @var string[] */
        public readonly array $spamDetectionFortguardLanguages,
        public readonly bool $spamDetectionFortguardLanguagesExclude,
        /** @var string[] */
        public readonly array $spamDetectionFortguardCountries,
        public readonly bool $spamDetectionFortguardCountriesExclude,
        /** @var string[] */
        public readonly array $spamDetectionFortguardSentiments,

        public readonly VoteType $voteType,
        public readonly bool $isVoteViewingOn,
        public readonly bool $isGuestVotingOn,
        public readonly Sort $sort,
        public readonly string $language,
        public readonly ?string $note,
        public readonly int $closeAfterDays,

        public readonly ?string $colorText,
        public readonly ?string $colorBackgroundText,
        public readonly ?string $colorAccent,
        public readonly ?string $colorAccentText,
        public readonly ?string $colorBox,
        public readonly ?string $colorBoxText,
        public readonly ?string $colorBoxTextLight,
        public readonly ?string $colorInput,

        public readonly ?string $colorDarkText,
        public readonly ?string $colorDarkBackgroundText,
        public readonly ?string $colorDarkAccent,
        public readonly ?string $colorDarkAccentText,
        public readonly ?string $colorDarkBox,
        public readonly ?string $colorDarkBoxText,
        public readonly ?string $colorDarkBoxTextLight,
        public readonly ?string $colorDarkInput,

        public readonly ColorTheme $colorTheme,
        public readonly ?string $ratingsColor,

        public readonly ?string $reaction1,
        public readonly ?string $reaction2,
        public readonly ?string $reaction3,
        public readonly ?string $reaction4,
        public readonly ?string $reaction5,
        public readonly ?string $reaction6,

        public readonly bool $isReaction1On,
        public readonly bool $isReaction2On,
        public readonly bool $isReaction3On,
        public readonly bool $isReaction4On,
        public readonly bool $isReaction5On,
        public readonly bool $isReaction6On,

        public readonly ?string $reaction1Text,
        public readonly ?string $reaction2Text,
        public readonly ?string $reaction3Text,
        public readonly ?string $reaction4Text,
        public readonly ?string $reaction5Text,
        public readonly ?string $reaction6Text,

        public readonly ReactionDisplayType $reactionDisplayType,

        public readonly bool $emailSendToUsers,
        public readonly NotifChannel $notifChannel,
        public readonly string $emailReportFrequency,
        public readonly ?string $emailCompanyName,
        public readonly ?string $emailCompanyLogoUrl,
        public readonly ?string $emailCompanyAddress,
        public readonly ?string $emailAlternateAddress,
        public readonly ?string $emailWebsiteUrl,
        public readonly ?string $emailNotificationSendingAddress,

        public readonly ?string $encryptionKey,
        public readonly ?string $dataApiKey,
        public readonly bool $isDataApiPublic,
        public readonly ?string $consoleApiKey,

        public readonly ?string $webhookUrl,
        public readonly bool $webhookOnCreate,
        public readonly bool $webhookOnEdit,
        public readonly bool $webhookOnDelete,
        public readonly ?string $webhookSecret,

        public readonly ?int $modBadgeId,
        public readonly ?string $modAliasName,
        public readonly ?string $modAliasPictureUrl,

        public readonly bool $membershipsEnabled,
        public readonly MembershipsCurrency $membershipsCurrency,
        public readonly ?int $membershipsYearlyDiscount,
        public readonly ?string $membershipsTextButton,
        public readonly ?string $membershipsTextModalTitle,
        public readonly ?string $membershipsTextModalTitleMembers,
        public readonly ?string $membershipsTextLoginToSubscribe,
        public readonly ?string $membershipsTextSubscribeAs,
        public readonly ?string $membershipsTextManageSubscription,
        public readonly ?string $membershipsTextPaymentSuccess,
        public readonly ?string $membershipsPaymentSuccessUrl,
        public readonly bool $membershipsGatedAllowGoogle,

        public readonly bool $emailReportDaily,
        public readonly bool $emailReportWeekly,
        public readonly bool $emailReportMonthly,

        public readonly bool $highlightNew,
        public readonly ?string $highlightNewColor,
        public readonly ?int $highlightUpvoteThreshold1,
        public readonly ?string $highlightUpvoteThreshold1Color,
        public readonly ?int $highlightUpvoteThreshold2,
        public readonly ?string $highlightUpvoteThreshold2Color,
    ) {
    }

    /**
     * @param array<mixed> $data
     * @internal
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            name: (string) $data['name'],

            authType: AuthType::from($data['auth_type']),
            authSsoType: isset($data['auth_sso_type']) ? AuthSsoType::from($data['auth_sso_type']) : null,
            ssoStatelessPrivateKey: $data['sso_stateless_private_key'] ?? null,
            ssoStatelessLoginUrl: $data['sso_stateless_login_url'] ?? null,
            ssoStatelessIsKeyless: (bool) $data['sso_stateless_is_keyless'],
            ssoOpenidIssuerUrl: $data['sso_openid_issuer_url'] ?? null,
            ssoOpenidClientId: $data['sso_openid_client_id'] ?? null,
            ssoOpenidClientSecret: $data['sso_openid_client_secret'] ?? null,

            premoderationStatus: PremoderationStatus::from($data['premoderation_status']),
            isRealtimeOn: (bool) $data['is_realtime_on'],
            realtimeTyping: RealtimeTyping::from($data['realtime_typing']),
            isRealtimeOnlineCountOn: (bool) $data['is_realtime_online_count_on'],
            isRealtimeUsersOn: (bool) $data['is_realtime_users_on'],
            isUserProfileOn: (bool) $data['is_user_profile_on'],
            isKeyboardNavigationOn: (bool) $data['is_keyboard_navigation_on'],
            isIpCollectionOn: (bool) $data['is_ip_collection_on'],
            isGuestCommentingOn: (bool) $data['is_guest_commenting_on'],
            guestCommentingEmail: GuestCommentingEmail::from($data['guest_commenting_email']),
            guestCommentingDelay: (int) $data['guest_commenting_delay'],
            defaultAvatar: $data['default_avatar'] ?? null,

            textCommentBox: $data['text_comment_box'] ?? null,
            textNoComments: $data['text_no_comments'] ?? null,
            textCommentButton: $data['text_comment_button'] ?? null,
            textReplyBox: $data['text_reply_box'] ?? null,
            textReplyButton: $data['text_reply_button'] ?? null,
            textReactions: $data['text_reactions'] ?? null,
            textRatings: $data['text_ratings'] ?? null,
            textCommentCount0: $data['text_comment_count_0'] ?? null,
            textCommentCount1: $data['text_comment_count_1'] ?? null,
            textCommentCountMulti: $data['text_comment_count_multi'] ?? null,

            topWidget: TopWidget::from($data['top_widget']),

            uiBoxShadow: (int) $data['ui_box_shadow'],
            uiBoxRoundness: (int) $data['ui_box_roundness'],
            uiBoxBorderSize: (int) $data['ui_box_border_size'],
            uiBoxWidth: isset($data['ui_box_width']) ? (int) $data['ui_box_width'] : null,
            uiButtonRoundness: (int) $data['ui_button_roundness'],
            uiBoxBorderColor: (string) $data['ui_box_border_color'],
            uiCustomCss: $data['ui_custom_css'] ?? null,

            isChNewOn: (bool) $data['is_ch_new_on'],
            chNewColor: $data['ch_new_color'] ?? null,
            chUpvote1Threshold: isset($data['ch_upvote_1_threshold']) ? (int) $data['ch_upvote_1_threshold'] : null,
            chUpvote2Threshold: isset($data['ch_upvote_2_threshold']) ? (int) $data['ch_upvote_2_threshold'] : null,
            chUpvote1Color: $data['ch_upvote_1_color'] ?? null,
            chUpvote2Color: $data['ch_upvote_2_color'] ?? null,

            isProfilePicturesOn: (bool) $data['is_profile_pictures_on'],

            displayNameType: DisplayNameType::from($data['display_name_type']),
            commentsPerRequest: (int) $data['comments_per_request'],
            repliesPerRequest: (int) $data['replies_per_request'],
            nestedLevels: (int) $data['nested_levels'],
            displayRepliedToType: DisplayRepliedToType::from($data['display_replied_to_type']),
            commentsCharLimit: (int) $data['comments_char_limit'],
            commentsMinCharLimit: (int) $data['comments_min_char_limit'],

            commentsEditingEnabled: (bool) $data['comments_editing_enabled'],
            commentsEditingTimeout: (int) $data['comments_editing_timeout'],
            commentsForceGuestCommenting: (bool) $data['comments_force_guest_commenting'],

            isEmojiEnabled: (bool) $data['is_emoji_enabled'],
            isImagesEnabled: (bool) $data['is_images_enabled'],
            isEmbedEnabled: (bool) $data['is_embed_enabled'],
            isMathEnabled: (bool) $data['is_math_enabled'],

            isSpamDetectionOn: (bool) $data['is_spam_detection_on'],
            spamDetectionProvider: SpamDetectionProvider::from($data['spam_detection_provider']),
            spamDetectionFortguardContentScore: isset($data['spam_detection_fortguard_content_score'])
                ? (int) $data['spam_detection_fortguard_content_score']
                : null,
            spamDetectionFortguardLanguages: $data['spam_detection_fortguard_languages'] ?? [],
            spamDetectionFortguardLanguagesExclude: (bool) $data['spam_detection_fortguard_languages_exclude'],
            spamDetectionFortguardCountries: $data['spam_detection_fortguard_countries'] ?? [],
            spamDetectionFortguardCountriesExclude: (bool) $data['spam_detection_fortguard_countries_exclude'],
            spamDetectionFortguardSentiments: $data['spam_detection_fortguard_sentiments'] ?? [],

            voteType: VoteType::from($data['vote_type']),
            isVoteViewingOn: (bool) $data['is_vote_viewing_on'],
            isGuestVotingOn: (bool) $data['is_guest_voting_on'],
            sort: Sort::from($data['sort']),
            language: (string) $data['language'],
            note: $data['note'] ?? null,
            closeAfterDays: (int) $data['close_after_days'],

            colorText: $data['color_text'] ?? null,
            colorBackgroundText: $data['color_background_text'] ?? null,
            colorAccent: $data['color_accent'] ?? null,
            colorAccentText: $data['color_accent_text'] ?? null,
            colorBox: $data['color_box'] ?? null,
            colorBoxText: $data['color_box_text'] ?? null,
            colorBoxTextLight: $data['color_box_text_light'] ?? null,
            colorInput: $data['color_input'] ?? null,

            colorDarkText: $data['color_dark_text'] ?? null,
            colorDarkBackgroundText: $data['color_dark_background_text'] ?? null,
            colorDarkAccent: $data['color_dark_accent'] ?? null,
            colorDarkAccentText: $data['color_dark_accent_text'] ?? null,
            colorDarkBox: $data['color_dark_box'] ?? null,
            colorDarkBoxText: $data['color_dark_box_text'] ?? null,
            colorDarkBoxTextLight: $data['color_dark_box_text_light'] ?? null,
            colorDarkInput: $data['color_dark_input'] ?? null,

            colorTheme: ColorTheme::from($data['color_theme']),
            ratingsColor: $data['ratings_color'] ?? null,

            reaction1: $data['reaction_1'] ?? null,
            reaction2: $data['reaction_2'] ?? null,
            reaction3: $data['reaction_3'] ?? null,
            reaction4: $data['reaction_4'] ?? null,
            reaction5: $data['reaction_5'] ?? null,
            reaction6: $data['reaction_6'] ?? null,

            isReaction1On: (bool) $data['is_reaction_1_on'],
            isReaction2On: (bool) $data['is_reaction_2_on'],
            isReaction3On: (bool) $data['is_reaction_3_on'],
            isReaction4On: (bool) $data['is_reaction_4_on'],
            isReaction5On: (bool) $data['is_reaction_5_on'],
            isReaction6On: (bool) $data['is_reaction_6_on'],

            reaction1Text: $data['reaction_1_text'] ?? null,
            reaction2Text: $data['reaction_2_text'] ?? null,
            reaction3Text: $data['reaction_3_text'] ?? null,
            reaction4Text: $data['reaction_4_text'] ?? null,
            reaction5Text: $data['reaction_5_text'] ?? null,
            reaction6Text: $data['reaction_6_text'] ?? null,

            reactionDisplayType: ReactionDisplayType::from($data['reaction_display_type']),

            emailSendToUsers: (bool) $data['email_send_to_users'],
            notifChannel: NotifChannel::from($data['notif_channel']),
            emailReportFrequency: (string) $data['email_report_frequency'],
            emailCompanyName: $data['email_company_name'] ?? null,
            emailCompanyLogoUrl: $data['email_company_logo_url'] ?? null,
            emailCompanyAddress: $data['email_company_address'] ?? null,
            emailAlternateAddress: $data['email_alternate_address'] ?? null,
            emailWebsiteUrl: $data['email_website_url'] ?? null,
            emailNotificationSendingAddress: $data['email_notification_sending_address'] ?? null,

            encryptionKey: $data['encryption_key'] ?? null,
            dataApiKey: $data['data_api_key'] ?? null,
            isDataApiPublic: (bool) $data['is_data_api_public'],
            consoleApiKey: $data['console_api_key'] ?? null,

            webhookUrl: $data['webhook_url'] ?? null,
            webhookOnCreate: (bool) $data['webhook_on_create'],
            webhookOnEdit: (bool) $data['webhook_on_edit'],
            webhookOnDelete: (bool) $data['webhook_on_delete'],
            webhookSecret: $data['webhook_secret'] ?? null,

            modBadgeId: isset($data['mod_badge_id']) ? (int) $data['mod_badge_id'] : null,
            modAliasName: $data['mod_alias_name'] ?? null,
            modAliasPictureUrl: $data['mod_alias_picture_url'] ?? null,

            membershipsEnabled: (bool) $data['memberships_enabled'],
            membershipsCurrency: MembershipsCurrency::from($data['memberships_currency']),
            membershipsYearlyDiscount: isset($data['memberships_yearly_discount'])
                ? (int) $data['memberships_yearly_discount']
                : null,
            membershipsTextButton: $data['memberships_text_button'] ?? null,
            membershipsTextModalTitle: $data['memberships_text_modal_title'] ?? null,
            membershipsTextModalTitleMembers: $data['memberships_text_modal_title_members'] ?? null,
            membershipsTextLoginToSubscribe: $data['memberships_text_login_to_subscribe'] ?? null,
            membershipsTextSubscribeAs: $data['memberships_text_subscribe_as'] ?? null,
            membershipsTextManageSubscription: $data['memberships_text_manage_subscription'] ?? null,
            membershipsTextPaymentSuccess: $data['memberships_text_payment_success'] ?? null,
            membershipsPaymentSuccessUrl: $data['memberships_payment_success_url'] ?? null,
            membershipsGatedAllowGoogle: (bool) $data['memberships_gated_allow_google'],

            emailReportDaily: (bool) $data['email_report_daily'],
            emailReportWeekly: (bool) $data['email_report_weekly'],
            emailReportMonthly: (bool) $data['email_report_monthly'],

            highlightNew: (bool) $data['highlight_new'],
            highlightNewColor: $data['highlight_new_color'] ?? null,
            highlightUpvoteThreshold1: isset($data['highlight_upvote_threshold_1'])
                ? (int) $data['highlight_upvote_threshold_1']
                : null,
            highlightUpvoteThreshold1Color: $data['highlight_upvote_threshold_1_color'] ?? null,
            highlightUpvoteThreshold2: isset($data['highlight_upvote_threshold_2'])
                ? (int) $data['highlight_upvote_threshold_2']
                : null,
            highlightUpvoteThreshold2Color: $data['highlight_upvote_threshold_2_color'] ?? null,
        );
    }
}
