<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Http\UploadedFile;
use Hyvor\Sdk\RequestOptions;
use Hyvor\Sdk\Talk\Dto\Job\ExportDataRequest;
use Hyvor\Sdk\Talk\Dto\Job\ImportCommentsRequest;
use Hyvor\Sdk\Talk\Dto\Job\Job;
use Hyvor\Sdk\Talk\Dto\Job\ListJobsRequest;

/**
 * `$client->talk->website($websiteId)->jobs`
 */
final class JobsResource extends WebsiteScopedResource
{
    /**
     * GET /jobs
     *
     * @return Job[]
     * @throws HyvorApiException
     */
    public function list(?ListJobsRequest $request = null, ?RequestOptions $options = null): array
    {
        $body = $this->transport->normalize($request ?? new ListJobsRequest());
        $data = $this->request('GET', $this->path('/jobs'), $body, $options);

        return $this->transport->denormalizeList($data, Job::class);
    }

    /**
     * POST /data/import/comments
     *
     * Importing is asynchronous; the website owner is notified by email
     * when it's done.
     *
     * @throws HyvorApiException
     */
    public function importComments(
        UploadedFile $file,
        ImportCommentsRequest $request,
        ?RequestOptions $options = null,
    ): Job {
        $data = $this->requestMultipart(
            'POST',
            $this->path('/data/import/comments'),
            [
                'format' => $request->format->value,
                'identifier_type' => $request->identifier_type?->value,
            ],
            ['file' => $file],
            $options,
        );

        return $this->transport->denormalize($data, Job::class);
    }

    /**
     * POST /data/export
     *
     * Exporting is asynchronous; a download link is emailed when it's ready.
     *
     * @throws HyvorApiException
     */
    public function export(ExportDataRequest $request, ?RequestOptions $options = null): Job
    {
        $body = $this->transport->normalize($request);
        $data = $this->request('POST', $this->path('/data/export'), $body, $options);

        return $this->transport->denormalize($data, Job::class);
    }
}
