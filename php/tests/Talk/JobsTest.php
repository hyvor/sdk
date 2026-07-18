<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Talk;

use Hyvor\Sdk\Http\UploadedFile;
use Hyvor\Sdk\Talk\Dto\Job\ExportDataRequest;
use Hyvor\Sdk\Talk\Dto\Job\ExportFormat;
use Hyvor\Sdk\Talk\Dto\Job\ImportCommentsFormat;
use Hyvor\Sdk\Talk\Dto\Job\ImportCommentsRequest;
use Hyvor\Sdk\Talk\Dto\Job\JobStatus;
use Hyvor\Sdk\Talk\Dto\Job\JobType;
use Hyvor\Sdk\Talk\Dto\Job\ListJobsRequest;
use Hyvor\Sdk\Tests\Support\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\TalkTestCase;

final class JobsTest extends TalkTestCase
{
    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function sampleJob(array $overrides = []): array
    {
        return array_merge([
            'id' => 1,
            'created_at' => 1700000000,
            'started_at' => null,
            'completed_at' => null,
            'failed_at' => null,
            'data' => [],
            'result' => null,
            'type' => 'import_comments',
            'status' => 'pending',
            'error' => null,
        ], $overrides);
    }

    public function testList(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [$this->sampleJob()]);

        $jobs = $this->client($http)->talk->website(self::WEBSITE_ID)->jobs->list(
            new ListJobsRequest(type: JobType::IMPORT_COMMENTS),
        );

        self::assertCount(1, $jobs);
        self::assertSame(JobStatus::PENDING, $jobs[0]->status);
        self::assertSame($this->baseUrl() . '/jobs', (string) $http->requests[0]->getUri());
    }

    public function testImportCommentsSendsMultipart(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleJob());

        $job = $this->client($http)->talk->website(self::WEBSITE_ID)->jobs->importComments(
            new UploadedFile('export.xml', '<xml/>', 'text/xml'),
            new ImportCommentsRequest(format: ImportCommentsFormat::WORDPRESS),
        );

        self::assertSame(1, $job->id);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/data/import/comments', (string) $request->getUri());
        self::assertStringStartsWith('multipart/form-data; boundary=', $request->getHeaderLine('Content-Type'));

        $body = (string) $request->getBody();
        self::assertStringContainsString('name="format"', $body);
        self::assertStringContainsString('wordpress', $body);
        self::assertStringContainsString('filename="export.xml"', $body);
        self::assertStringContainsString('<xml/>', $body);
    }

    public function testExport(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleJob(['type' => 'export']));

        $job = $this->client($http)->talk->website(self::WEBSITE_ID)->jobs->export(
            new ExportDataRequest(format: ExportFormat::HYVOR_TALK_JSON),
        );

        self::assertSame(JobType::EXPORT, $job->type);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/data/export', (string) $request->getUri());
        self::assertSame(['format' => 'hyvor_talk_json'], json_decode((string) $request->getBody(), true));
    }
}
