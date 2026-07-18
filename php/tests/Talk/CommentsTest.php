<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Talk;

use Hyvor\Sdk\Talk\Dto\Comment\BulkModerateRequest;
use Hyvor\Sdk\Talk\Dto\Comment\CommentStatus;
use Hyvor\Sdk\Talk\Dto\Comment\CreateCommentRequest;
use Hyvor\Sdk\Talk\Dto\Comment\CreateFlagRequest;
use Hyvor\Sdk\Talk\Dto\Comment\GetFlagsRequest;
use Hyvor\Sdk\Talk\Dto\Comment\GetVotersRequest;
use Hyvor\Sdk\Talk\Dto\Comment\ListCommentsRequest;
use Hyvor\Sdk\Talk\Dto\Comment\MarkCommentsReadRequest;
use Hyvor\Sdk\Talk\Dto\Comment\ReplyToCommentRequest;
use Hyvor\Sdk\Talk\Dto\Comment\UpdateCommentRequest;
use Hyvor\Sdk\Talk\Dto\Comment\VoteDirection;
use Hyvor\Sdk\Talk\Dto\Comment\VoteOnCommentRequest;
use Hyvor\Sdk\Tests\Support\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\TalkTestCase;

final class CommentsTest extends TalkTestCase
{
    public function testListSendsFilters(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [$this->sampleComment()]);

        $comments = $this->client($http)->talk->website(self::WEBSITE_ID)->comments->list(
            new ListCommentsRequest(search: 'hello', limit: 10),
        );

        self::assertCount(1, $comments);
        self::assertSame(5, $comments[0]->id);
        self::assertSame(CommentStatus::PUBLISHED, $comments[0]->status);

        $request = $http->requests[0];
        self::assertSame('GET', $request->getMethod());
        self::assertSame($this->baseUrl() . '/comments', (string) $request->getUri());
        self::assertSame(
            ['search' => 'hello', 'limit' => 10],
            json_decode((string) $request->getBody(), true),
        );
    }

    public function testUnreadCounts(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, ['published' => 1, 'pending' => 2, 'spam' => 3, 'deleted' => 4]);

        $counts = $this->client($http)->talk->website(self::WEBSITE_ID)->comments->unreadCounts();

        self::assertSame(1, $counts->published);
        self::assertSame($this->baseUrl() . '/comments/unread-counts', (string) $http->requests[0]->getUri());
    }

    public function testMarkRead(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->talk->website(self::WEBSITE_ID)->comments->markRead(
            new MarkCommentsReadRequest(status: CommentStatus::PENDING),
        );

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/comments/read', (string) $request->getUri());
        self::assertSame(['status' => 'pending'], json_decode((string) $request->getBody(), true));
    }

    public function testCreate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleComment());

        $comment = $this->client($http)->talk->website(self::WEBSITE_ID)->comments->create(
            new CreateCommentRequest(page_id: 1, body: 'Hello', guest_name: 'Guest'),
        );

        self::assertSame(5, $comment->id);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/comment', (string) $request->getUri());
        self::assertSame(
            ['page_id' => 1, 'body' => 'Hello', 'guest_name' => 'Guest', 'check_premoderation' => true, 'spam_detection' => true, 'rules' => true],
            json_decode((string) $request->getBody(), true),
        );
    }

    public function testGet(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleComment());

        $comment = $this->client($http)->talk->website(self::WEBSITE_ID)->comments->get(5);

        self::assertSame(5, $comment->id);
        self::assertSame($this->baseUrl() . '/comment/5', (string) $http->requests[0]->getUri());
    }

    public function testUpdate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleComment(['status' => 'spam']));

        $comment = $this->client($http)->talk->website(self::WEBSITE_ID)->comments->update(
            5,
            new UpdateCommentRequest(status: CommentStatus::SPAM),
        );

        self::assertSame(CommentStatus::SPAM, $comment->status);

        $request = $http->requests[0];
        self::assertSame('PATCH', $request->getMethod());
        self::assertSame($this->baseUrl() . '/comment/5', (string) $request->getUri());
        self::assertSame(
            ['status' => 'spam', 'is_author' => false, 'check_premoderation' => true, 'spam_detection' => true, 'rules' => true],
            json_decode((string) $request->getBody(), true),
        );
    }

    public function testDelete(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->talk->website(self::WEBSITE_ID)->comments->delete(5);

        $request = $http->requests[0];
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame($this->baseUrl() . '/comment/5', (string) $request->getUri());
    }

    public function testReply(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleComment(['id' => 6]));

        $comment = $this->client($http)->talk->website(self::WEBSITE_ID)->comments->reply(
            5,
            new ReplyToCommentRequest(body: 'A reply'),
        );

        self::assertSame(6, $comment->id);
        self::assertSame($this->baseUrl() . '/comment/5/reply', (string) $http->requests[0]->getUri());
        self::assertSame(['body' => 'A reply'], json_decode((string) $http->requests[0]->getBody(), true));
    }

    public function testVoteSendsExplicitNullType(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleComment());

        $this->client($http)->talk->website(self::WEBSITE_ID)->comments->vote(
            5,
            new VoteOnCommentRequest(type: null, user_sso_id: 'u1'),
        );

        $request = $http->requests[0];
        self::assertSame($this->baseUrl() . '/comment/5/vote', (string) $request->getUri());
        // type: null must be sent verbatim (it means "remove vote"), unlike
        // most other request DTOs where null fields are skipped.
        self::assertSame(['type' => null, 'user_sso_id' => 'u1'], json_decode((string) $request->getBody(), true));
    }

    public function testVoteSendsType(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleComment());

        $this->client($http)->talk->website(self::WEBSITE_ID)->comments->vote(
            5,
            new VoteOnCommentRequest(type: VoteDirection::UP),
        );

        self::assertSame(
            ['type' => 'up', 'user_sso_id' => null],
            json_decode((string) $http->requests[0]->getBody(), true),
        );
    }

    public function testVoters(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [$this->sampleLoggedInUser()]);

        $voters = $this->client($http)->talk->website(self::WEBSITE_ID)->comments->voters(
            5,
            new GetVotersRequest(type: VoteDirection::UP),
        );

        self::assertCount(1, $voters);
        self::assertSame('Bob', $voters[0]->name);
        self::assertSame($this->baseUrl() . '/comment/5/voters', (string) $http->requests[0]->getUri());
    }

    public function testDeleteVote(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->talk->website(self::WEBSITE_ID)->comments->deleteVote(5, 'hyvor_1');

        $request = $http->requests[0];
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame($this->baseUrl() . '/comment/5/vote', (string) $request->getUri());
        self::assertSame(['user_htid' => 'hyvor_1'], json_decode((string) $request->getBody(), true));
    }

    public function testFlags(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [
            ['id' => 1, 'user' => $this->sampleGuestUser(), 'comment_id' => 5, 'reason' => 'spam', 'has_mod_seen' => false],
        ]);

        $flags = $this->client($http)->talk->website(self::WEBSITE_ID)->comments->flags(5, new GetFlagsRequest());

        self::assertCount(1, $flags);
        self::assertSame('spam', $flags[0]->reason);
        self::assertSame($this->baseUrl() . '/comment/5/flags', (string) $http->requests[0]->getUri());
    }

    public function testCreateFlag(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleComment());

        $this->client($http)->talk->website(self::WEBSITE_ID)->comments->createFlag(
            5,
            new CreateFlagRequest(reason: 'spam'),
        );

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/comment/5/flag', (string) $request->getUri());
        self::assertSame(['reason' => 'spam'], json_decode((string) $request->getBody(), true));
    }

    public function testDeleteFlag(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->talk->website(self::WEBSITE_ID)->comments->deleteFlag(5, 9);

        $request = $http->requests[0];
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame($this->baseUrl() . '/comment/5/flag', (string) $request->getUri());
        self::assertSame(['flag_id' => 9], json_decode((string) $request->getBody(), true));
    }

    public function testBulkModerate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->talk->website(self::WEBSITE_ID)->comments->bulkModerate(
            new BulkModerateRequest(status: CommentStatus::SPAM, comment_ids: [1, 2, 3]),
        );

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/comments/bulk-moderate', (string) $request->getUri());
        self::assertSame(
            ['status' => 'spam', 'comment_ids' => [1, 2, 3]],
            json_decode((string) $request->getBody(), true),
        );
    }
}
