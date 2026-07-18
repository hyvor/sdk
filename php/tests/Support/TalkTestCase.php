<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Support;

use Hyvor\Sdk\Auth\StaticTokenProvider;
use Hyvor\Sdk\HyvorClient;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * Shared helpers for Talk resource tests: a preconfigured client and sample
 * JSON payloads for objects that are nested inside many endpoint responses
 * (User, Page, ...), so each test file doesn't have to redefine them.
 */
abstract class TalkTestCase extends TestCase
{
    protected const WEBSITE_ID = 42;

    protected function client(FakeHttpClient $httpClient, int $retryMaxAttempts = 3): HyvorClient
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

    protected function baseUrl(): string
    {
        return 'https://talk.hyvor.com/api/console/v1/' . self::WEBSITE_ID;
    }

    /**
     * @param array<mixed> $data
     */
    protected function queueJson(FakeHttpClient $http, array $data, int $status = 200): void
    {
        $http->queueResponse(new Response($status, [], json_encode($data, JSON_THROW_ON_ERROR)));
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    protected function sampleGuestUser(array $overrides = []): array
    {
        return array_merge([
            'type' => null,
            'name' => 'Guest',
            'email' => null,
            'picture_url' => null,
        ], $overrides);
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    protected function sampleLoggedInUser(array $overrides = []): array
    {
        return array_merge([
            'type' => 'hyvor',
            'id' => 1,
            'htid' => 'hyvor_1',
            'sso_id' => '',
            'name' => 'Bob',
            'title' => null,
            'username' => 'bob',
            'email' => null,
            'picture_url' => null,
            'bio' => null,
            'location' => null,
            'website_url' => null,
            'created_at' => 1700000000,
            'last_commented_at' => null,
            'comments_count' => 0,
            'state' => 'default',
            'state_ends_at' => null,
            'note' => null,
            'ban_reason' => null,
            'reputation' => 0,
            'badge_ids' => [],
            'register_ip' => null,
            'last_ip' => null,
            'days_visited' => 0,
            'last_seen_at' => null,
            'emails_sent' => 0,
            'last_emailed_at' => null,
            'role' => null,
        ], $overrides);
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    protected function sampleUserMini(array $overrides = []): array
    {
        return array_merge([
            'id' => 1,
            'type' => 'hyvor',
            'htid' => 'hyvor_1',
            'name' => 'Bob',
            'username' => 'bob',
            'picture_url' => null,
        ], $overrides);
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    protected function samplePage(array $overrides = []): array
    {
        return array_merge([
            'id' => 1,
            'created_at' => 1700000000,
            'last_commented_at' => null,
            'title' => 'My Post',
            'identifier' => 'post-1',
            'url' => 'https://example.com/post-1',
            'is_closed' => false,
            'is_premoderation_on' => false,
            'author_email' => null,
            'comments_count' => 0,
            'ratings' => ['average' => 0, 'count' => 0],
            'reactions' => ['superb' => 0, 'love' => 0, 'wow' => 0, 'sad' => 0, 'laugh' => 0, 'angry' => 0],
        ], $overrides);
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    protected function sampleComment(array $overrides = []): array
    {
        return array_merge([
            'id' => 5,
            'created_at' => 1700000000,
            'user' => $this->sampleGuestUser(),
            'page' => $this->samplePage(),
            'body' => 'Hello',
            'body_html' => '<p>Hello</p>',
            'status' => 'published',
            'parent' => null,
            'ip_address' => null,
            'has_mod_seen' => true,
            'has_mod_replied' => false,
            'is_featured' => false,
            'is_loved' => false,
            'is_edited' => false,
            'has_questions' => false,
            'has_links' => false,
            'has_media' => false,
            'is_automatic_spam' => false,
            'flags_count' => 0,
            'last_flag_at' => null,
            'upvotes' => 0,
            'downvotes' => 0,
            'user_vote' => null,
            'history' => [],
        ], $overrides);
    }
}
