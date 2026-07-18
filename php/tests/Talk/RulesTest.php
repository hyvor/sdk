<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Talk;

use Hyvor\Sdk\Talk\Dto\Comment\CommentStatus;
use Hyvor\Sdk\Talk\Dto\Rule\RuleType;
use Hyvor\Sdk\Talk\Dto\Rule\UpsertRuleRequest;
use Hyvor\Sdk\Tests\Support\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\TalkTestCase;

final class RulesTest extends TalkTestCase
{
    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function sampleRule(array $overrides = []): array
    {
        return array_merge([
            'id' => 1,
            'type' => 'word_matches',
            'value' => 'badword',
            'to_status' => 'spam',
            'priority' => 1,
        ], $overrides);
    }

    public function testList(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [$this->sampleRule()]);

        $rules = $this->client($http)->talk->website(self::WEBSITE_ID)->rules->list();

        self::assertCount(1, $rules);
        self::assertSame(RuleType::WORD_MATCHES, $rules[0]->type);
        self::assertSame($this->baseUrl() . '/rules', (string) $http->requests[0]->getUri());
    }

    public function testCreate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleRule());

        $rule = $this->client($http)->talk->website(self::WEBSITE_ID)->rules->create(
            new UpsertRuleRequest(type: RuleType::WORD_MATCHES, value: 'badword', to_status: CommentStatus::SPAM, priority: 1),
        );

        self::assertSame(1, $rule->id);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/rule', (string) $request->getUri());
    }

    public function testUpdate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleRule(['priority' => 5]));

        $rule = $this->client($http)->talk->website(self::WEBSITE_ID)->rules->update(
            1,
            new UpsertRuleRequest(type: RuleType::WORD_MATCHES, value: 'badword', to_status: CommentStatus::SPAM, priority: 5),
        );

        self::assertSame(5, $rule->priority);
        self::assertSame($this->baseUrl() . '/rule/1', (string) $http->requests[0]->getUri());
    }

    public function testDelete(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->talk->website(self::WEBSITE_ID)->rules->delete(1);

        $request = $http->requests[0];
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame($this->baseUrl() . '/rule/1', (string) $request->getUri());
    }
}
