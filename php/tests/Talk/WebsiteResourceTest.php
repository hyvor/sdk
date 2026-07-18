<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Talk;

use Hyvor\Sdk\Auth\StaticTokenProvider;
use Hyvor\Sdk\Exceptions\RateLimitException;
use Hyvor\Sdk\Exceptions\ServerErrorException;
use Hyvor\Sdk\Exceptions\ValidationFailedException;
use Hyvor\Sdk\HyvorClient;
use Hyvor\Sdk\RequestOptions;
use Hyvor\Sdk\Talk\Dto\Website\CreateWebsiteRequest;
use Hyvor\Sdk\Tests\Support\FakeHttpClient;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class WebsiteResourceTest extends TestCase
{
    private function client(FakeHttpClient $httpClient, int $retryMaxAttempts = 3): HyvorClient
    {
        $factory = new Psr17Factory();

        return new HyvorClient(
            httpClient: $httpClient,
            requestFactory: $factory,
            streamFactory: $factory,
            tokenProvider: new StaticTokenProvider('test-jwt-token'),
            retryMaxAttempts: $retryMaxAttempts,
        );
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function sampleWebsiteData(array $overrides = []): array
    {
        return array_merge([
            'id' => 42,
            'name' => 'My Blog',

            'auth_type' => 'hyvor',
            'auth_sso_type' => null,
            'sso_stateless_private_key' => null,
            'sso_stateless_login_url' => null,
            'sso_stateless_is_keyless' => false,
            'sso_openid_issuer_url' => null,
            'sso_openid_client_id' => null,
            'sso_openid_client_secret' => null,

            'premoderation_status' => 'off',
            'is_realtime_on' => true,
            'realtime_typing' => 'on_with_typer',
            'is_realtime_online_count_on' => true,
            'is_realtime_users_on' => true,
            'is_user_profile_on' => true,
            'is_keyboard_navigation_on' => true,
            'is_ip_collection_on' => false,
            'is_guest_commenting_on' => true,
            'guest_commenting_email' => 'optional',
            'guest_commenting_delay' => 0,
            'default_avatar' => null,

            'text_comment_box' => null,
            'text_no_comments' => null,
            'text_comment_button' => null,
            'text_reply_box' => null,
            'text_reply_button' => null,
            'text_reactions' => null,
            'text_ratings' => null,
            'text_comment_count_0' => null,
            'text_comment_count_1' => null,
            'text_comment_count_multi' => null,

            'top_widget' => 'none',

            'ui_box_shadow' => 0,
            'ui_box_roundness' => 4,
            'ui_box_border_size' => 1,
            'ui_box_width' => null,
            'ui_button_roundness' => 4,
            'ui_box_border_color' => '#e5e5e5',
            'ui_custom_css' => null,

            'is_ch_new_on' => false,
            'ch_new_color' => null,
            'ch_upvote_1_threshold' => null,
            'ch_upvote_2_threshold' => null,
            'ch_upvote_1_color' => null,
            'ch_upvote_2_color' => null,

            'is_profile_pictures_on' => true,

            'display_name_type' => 'name',
            'comments_per_request' => 50,
            'replies_per_request' => 50,
            'nested_levels' => 3,
            'display_replied_to_type' => 'deep',
            'comments_char_limit' => 50000,
            'comments_min_char_limit' => 0,

            'comments_editing_enabled' => true,
            'comments_editing_timeout' => 300,
            'comments_force_guest_commenting' => false,

            'is_emoji_enabled' => true,
            'is_images_enabled' => true,
            'is_embed_enabled' => true,
            'is_math_enabled' => false,

            'is_spam_detection_on' => true,
            'spam_detection_provider' => 'none',
            'spam_detection_fortguard_content_score' => null,
            'spam_detection_fortguard_languages' => [],
            'spam_detection_fortguard_languages_exclude' => false,
            'spam_detection_fortguard_countries' => [],
            'spam_detection_fortguard_countries_exclude' => false,
            'spam_detection_fortguard_sentiments' => [],

            'vote_type' => 'both',
            'is_vote_viewing_on' => true,
            'is_guest_voting_on' => true,
            'sort' => 'newest',
            'language' => 'en-us',
            'note' => null,
            'close_after_days' => 0,

            'color_text' => null,
            'color_background_text' => null,
            'color_accent' => null,
            'color_accent_text' => null,
            'color_box' => null,
            'color_box_text' => null,
            'color_box_text_light' => null,
            'color_input' => null,

            'color_dark_text' => null,
            'color_dark_background_text' => null,
            'color_dark_accent' => null,
            'color_dark_accent_text' => null,
            'color_dark_box' => null,
            'color_dark_box_text' => null,
            'color_dark_box_text_light' => null,
            'color_dark_input' => null,

            'color_theme' => 'os',
            'ratings_color' => null,

            'reaction_1' => null,
            'reaction_2' => null,
            'reaction_3' => null,
            'reaction_4' => null,
            'reaction_5' => null,
            'reaction_6' => null,

            'is_reaction_1_on' => true,
            'is_reaction_2_on' => true,
            'is_reaction_3_on' => true,
            'is_reaction_4_on' => true,
            'is_reaction_5_on' => true,
            'is_reaction_6_on' => true,

            'reaction_1_text' => null,
            'reaction_2_text' => null,
            'reaction_3_text' => null,
            'reaction_4_text' => null,
            'reaction_5_text' => null,
            'reaction_6_text' => null,

            'reaction_display_type' => 'both',

            'email_send_to_users' => true,
            'notif_channel' => 'email',
            'email_report_frequency' => 'weekly',
            'email_company_name' => null,
            'email_company_logo_url' => null,
            'email_company_address' => null,
            'email_alternate_address' => null,
            'email_website_url' => null,
            'email_notification_sending_address' => null,

            'encryption_key' => null,
            'data_api_key' => null,
            'is_data_api_public' => false,
            'console_api_key' => null,

            'webhook_url' => null,
            'webhook_on_create' => false,
            'webhook_on_edit' => false,
            'webhook_on_delete' => false,
            'webhook_secret' => null,

            'mod_badge_id' => null,
            'mod_alias_name' => null,
            'mod_alias_picture_url' => null,

            'memberships_enabled' => false,
            'memberships_currency' => 'usd',
            'memberships_yearly_discount' => null,
            'memberships_text_button' => null,
            'memberships_text_modal_title' => null,
            'memberships_text_modal_title_members' => null,
            'memberships_text_login_to_subscribe' => null,
            'memberships_text_subscribe_as' => null,
            'memberships_text_manage_subscription' => null,
            'memberships_text_payment_success' => null,
            'memberships_payment_success_url' => null,
            'memberships_gated_allow_google' => false,

            'email_report_daily' => false,
            'email_report_weekly' => true,
            'email_report_monthly' => false,

            'highlight_new' => true,
            'highlight_new_color' => null,
            'highlight_upvote_threshold_1' => null,
            'highlight_upvote_threshold_1_color' => null,
            'highlight_upvote_threshold_2' => null,
            'highlight_upvote_threshold_2_color' => null,
        ], $overrides);
    }

    public function testGetReturnsWebsite(): void
    {
        $http = new FakeHttpClient();
        $http->queueResponse(new Response(200, [], json_encode(
            $this->sampleWebsiteData(),
            JSON_THROW_ON_ERROR,
        )));

        $client = $this->client($http);
        $website = $client->talk->website->get();

        self::assertSame(42, $website->id);
        self::assertSame('My Blog', $website->name);
        self::assertSame(\Hyvor\Sdk\Talk\Dto\Website\AuthType::HYVOR, $website->auth_type);
        self::assertSame(\Hyvor\Sdk\Talk\Dto\Website\ColorTheme::OS, $website->color_theme);

        self::assertCount(1, $http->requests);
        self::assertSame('GET', $http->requests[0]->getMethod());
        self::assertSame('https://hyvor.com/api/talk/website', (string) $http->requests[0]->getUri());
        self::assertSame('Bearer test-jwt-token', $http->requests[0]->getHeaderLine('Authorization'));
        self::assertStringStartsWith('hyvor/sdk-php/', $http->requests[0]->getHeaderLine('User-Agent'));
    }

    public function testCreateSendsNameAndDomain(): void
    {
        $http = new FakeHttpClient();
        $http->queueResponse(new Response(201, [], json_encode(
            $this->sampleWebsiteData(['id' => 7, 'name' => 'New Site']),
            JSON_THROW_ON_ERROR,
        )));

        $client = $this->client($http);
        $website = $client->talk->website->create(new CreateWebsiteRequest(
            name: 'New Site',
            domain: 'new.example.com',
        ));

        self::assertSame(7, $website->id);
        self::assertSame('New Site', $website->name);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame('https://hyvor.com/api/talk/website', (string) $request->getUri());
        self::assertSame(
            ['name' => 'New Site', 'domain' => 'new.example.com'],
            json_decode((string) $request->getBody(), true),
        );
    }

    public function testValidationErrorThrows(): void
    {
        $http = new FakeHttpClient();
        $http->queueResponse(new Response(422, [], json_encode([
            'message' => 'The given data was invalid.',
            'errors' => ['domain' => ['The domain field is required.']],
        ], JSON_THROW_ON_ERROR)));

        $client = $this->client($http);

        try {
            $client->talk->website->create(new CreateWebsiteRequest(name: 'X', domain: ''));
            self::fail('Expected ValidationFailedException to be thrown.');
        } catch (ValidationFailedException $e) {
            self::assertSame(422, $e->statusCode);
            self::assertSame(['The domain field is required.'], $e->errors['domain']);
        }
    }

    public function testRateLimitRetriesThenSucceeds(): void
    {
        $http = new FakeHttpClient();
        $http->queueResponse(new Response(429, ['retry-after' => '0'], json_encode(['message' => 'Too many requests'])));
        $http->queueResponse(new Response(200, [], json_encode(
            $this->sampleWebsiteData(['id' => 1, 'name' => 'Site']),
            JSON_THROW_ON_ERROR,
        )));

        $client = $this->client($http, retryMaxAttempts: 2);
        $website = $client->talk->website->get();

        self::assertSame(1, $website->id);
        self::assertCount(2, $http->requests);
    }

    public function testRateLimitExhaustsRetriesAndThrows(): void
    {
        $http = new FakeHttpClient();
        $http->queueResponse(new Response(429, ['retry-after' => '0'], json_encode(['message' => 'Too many requests'])));
        $http->queueResponse(new Response(429, ['retry-after' => '0'], json_encode(['message' => 'Too many requests'])));

        $client = $this->client($http, retryMaxAttempts: 2);

        $this->expectException(RateLimitException::class);
        $client->talk->website->get();
    }

    public function testServerErrorThrows(): void
    {
        $http = new FakeHttpClient();
        $http->queueResponse(new Response(500, [], json_encode(['message' => 'Internal error'])));
        $http->queueResponse(new Response(500, [], json_encode(['message' => 'Internal error'])));

        $client = $this->client($http, retryMaxAttempts: 2);

        $this->expectException(ServerErrorException::class);
        $client->talk->website->get();
    }
}
