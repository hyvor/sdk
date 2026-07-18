<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Post\Dto\Issue\Issue;
use Hyvor\Sdk\Post\Dto\Issue\ListIssuesRequest;
use Hyvor\Sdk\Post\Dto\Issue\Send;
use Hyvor\Sdk\Post\Dto\Issue\UpdateIssueRequest;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->post->newsletter($newsletterId)->issues`
 */
final class IssuesResource extends NewsletterScopedResource
{
    /**
     * GET /issues
     *
     * @return Issue[]
     * @throws HyvorApiException
     */
    public function list(?ListIssuesRequest $request = null, ?RequestOptions $options = null): array
    {
        $body = $this->transport->normalize($request ?? new ListIssuesRequest());
        $data = $this->request('GET', $this->path('/issues'), $body, $options);

        return $this->transport->denormalizeList($data, Issue::class);
    }

    /**
     * POST /issues
     *
     * Creates a blank draft issue; use `update()` to set its content.
     *
     * @throws HyvorApiException
     */
    public function create(?RequestOptions $options = null): Issue
    {
        $data = $this->request('POST', $this->path('/issues'), null, $options);

        return $this->transport->denormalize($data, Issue::class);
    }

    /**
     * GET /issues/{id}
     *
     * @throws HyvorApiException
     */
    public function get(int $id, ?RequestOptions $options = null): Issue
    {
        $data = $this->request('GET', $this->path("/issues/{$id}"), null, $options);

        return $this->transport->denormalize($data, Issue::class);
    }

    /**
     * PATCH /issues/{id}
     *
     * @throws HyvorApiException
     */
    public function update(int $id, UpdateIssueRequest $request, ?RequestOptions $options = null): Issue
    {
        $body = $this->transport->normalize($request);
        $data = $this->request('PATCH', $this->path("/issues/{$id}"), $body, $options);

        return $this->transport->denormalize($data, Issue::class);
    }

    /**
     * DELETE /issues/{id}
     *
     * @throws HyvorApiException
     */
    public function delete(int $id, ?RequestOptions $options = null): void
    {
        $this->request('DELETE', $this->path("/issues/{$id}"), null, $options);
    }

    /**
     * POST /issues/{id}/send
     *
     * @throws HyvorApiException
     */
    public function send(int $id, ?RequestOptions $options = null): Issue
    {
        $data = $this->request('POST', $this->path("/issues/{$id}/send"), null, $options);

        return $this->transport->denormalize($data, Issue::class);
    }

    /**
     * GET /issues/{id}/sends
     *
     * @return Send[]
     * @throws HyvorApiException
     */
    public function sends(int $id, ?RequestOptions $options = null): array
    {
        $data = $this->request('GET', $this->path("/issues/{$id}/sends"), null, $options);

        return $this->transport->denormalizeList($data, Send::class);
    }
}
