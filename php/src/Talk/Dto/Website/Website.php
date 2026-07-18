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
}
