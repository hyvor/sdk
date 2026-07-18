<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Talk;

use Hyvor\Sdk\Talk\Dto\EmailLog\EmailLogType;
use Hyvor\Sdk\Talk\Dto\EmailLog\ListEmailLogsRequest;
use Hyvor\Sdk\Tests\Support\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\TalkTestCase;

final class EmailLogsTest extends TalkTestCase
{
    public function testList(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [
            [
                'id' => 1,
                'created_at' => 1700000000,
                'comment_id' => 5,
                'type' => 'reply',
                'comment_url' => 'https://example.com/post-1#comment-5',
                'user' => $this->sampleLoggedInUser(),
                'guest_email' => null,
            ],
        ]);

        $logs = $this->client($http)->talk->website(self::WEBSITE_ID)->emailLogs->list(
            new ListEmailLogsRequest(type: EmailLogType::REPLY),
        );

        self::assertCount(1, $logs);
        self::assertSame(EmailLogType::REPLY, $logs[0]->type);
        self::assertSame($this->baseUrl() . '/email-logs', (string) $http->requests[0]->getUri());
        self::assertSame(['type' => 'reply'], json_decode((string) $http->requests[0]->getBody(), true));
    }
}
