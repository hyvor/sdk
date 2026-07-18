<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Post;

use Hyvor\Sdk\Post\Dto\Issue\IssueStatus;
use Hyvor\Sdk\Post\Dto\Issue\ListIssuesRequest;
use Hyvor\Sdk\Post\Dto\Issue\SendStatus;
use Hyvor\Sdk\Post\Dto\Issue\UpdateIssueRequest;
use Hyvor\Sdk\Tests\Support\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\PostTestCase;

final class IssuesTest extends PostTestCase
{
    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function sampleIssue(array $overrides = []): array
    {
        return array_merge([
            'id' => 1,
            'uuid' => 'issue-uuid-1',
            'created_at' => 1700000000,
            'subject' => 'Hello',
            'content' => '<p>Hello</p>',
            'sending_profile_id' => 1,
            'status' => 'draft',
            'lists' => [1, 2],
            'scheduled_at' => null,
            'sending_at' => null,
            'sent_at' => null,
            'total_sends' => 0,
            'from_email' => null,
            'from_name' => null,
            'reply_to_email' => null,
            'sendable_subscribers_count' => 100,
        ], $overrides);
    }

    public function testList(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [$this->sampleIssue()]);

        $issues = $this->client($http)->post->newsletter(self::NEWSLETTER_ID)->issues->list(
            new ListIssuesRequest(limit: 10),
        );

        self::assertCount(1, $issues);
        self::assertSame(IssueStatus::DRAFT, $issues[0]->status);
        self::assertSame($this->baseUrl() . '/issues', (string) $http->requests[0]->getUri());
        self::assertSame(['limit' => 10], json_decode((string) $http->requests[0]->getBody(), true));
    }

    public function testCreate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleIssue(), 201);

        $issue = $this->client($http)->post->newsletter(self::NEWSLETTER_ID)->issues->create();

        self::assertSame(1, $issue->id);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/issues', (string) $request->getUri());
        self::assertSame('', (string) $request->getBody());
    }

    public function testGet(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleIssue());

        $issue = $this->client($http)->post->newsletter(self::NEWSLETTER_ID)->issues->get(1);

        self::assertSame(1, $issue->id);
        self::assertSame($this->baseUrl() . '/issues/1', (string) $http->requests[0]->getUri());
    }

    public function testUpdate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleIssue(['subject' => 'Updated']));

        $issue = $this->client($http)->post->newsletter(self::NEWSLETTER_ID)->issues->update(
            1,
            new UpdateIssueRequest(subject: 'Updated', lists: [1, 2]),
        );

        self::assertSame('Updated', $issue->subject);

        $request = $http->requests[0];
        self::assertSame('PATCH', $request->getMethod());
        self::assertSame($this->baseUrl() . '/issues/1', (string) $request->getUri());
        self::assertSame(
            ['subject' => 'Updated', 'lists' => [1, 2]],
            json_decode((string) $request->getBody(), true),
        );
    }

    public function testDelete(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->post->newsletter(self::NEWSLETTER_ID)->issues->delete(1);

        $request = $http->requests[0];
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame($this->baseUrl() . '/issues/1', (string) $request->getUri());
    }

    public function testSend(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleIssue(['status' => 'sending']));

        $issue = $this->client($http)->post->newsletter(self::NEWSLETTER_ID)->issues->send(1);

        self::assertSame(IssueStatus::SENDING, $issue->status);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/issues/1/send', (string) $request->getUri());
    }

    public function testSends(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [
            [
                'id' => 1,
                'created_at' => 1700000000,
                'subscriber' => $this->sampleSubscriber(),
                'email' => 'jane@example.com',
                'status' => 'sent',
                'sent_at' => 1700000100,
                'failed_at' => null,
                'delivered_at' => 1700000200,
                'unsubscribed_at' => null,
                'bounced_at' => null,
                'hard_bounce' => false,
                'complained_at' => null,
            ],
        ]);

        $sends = $this->client($http)->post->newsletter(self::NEWSLETTER_ID)->issues->sends(1);

        self::assertCount(1, $sends);
        self::assertSame(SendStatus::SENT, $sends[0]->status);
        self::assertSame('jane@example.com', $sends[0]->subscriber?->email);
        self::assertSame($this->baseUrl() . '/issues/1/sends', (string) $http->requests[0]->getUri());
    }
}
