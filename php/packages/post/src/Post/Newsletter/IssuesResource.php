<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Newsletter;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Post\Dto\Issue;
use Hyvor\Sdk\Post\Dto\Send;
use Hyvor\Sdk\Post\Newsletter;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->newsletter($newsletterId)->issues`
 */
final class IssuesResource
{
    public function __construct(private readonly Newsletter $client)
    {
    }

    /**
     * GET /api/console/issues
     *
     * @return Issue[]
     *
     * @throws HyvorApiException
     */
    public function list(?RequestOptions $options = null): array
    {
        $result = $this->client->request('GET', $this->client->path('/api/console/issues'), null, $options);

        return $this->client->transport->denormalizeList($result, Issue::class);
    }

    /**
     * POST /api/console/issues
     *
     * @throws HyvorApiException
     */
    public function create(?RequestOptions $options = null): Issue
    {
        $result = $this->client->request('POST', $this->client->path('/api/console/issues'), null, $options);

        return $this->client->transport->denormalize($result, Issue::class);
    }

    /**
     * GET /api/console/issues/{id}
     *
     * @throws HyvorApiException
     */
    public function get(int $id, ?RequestOptions $options = null): Issue
    {
        $result = $this->client->request('GET', $this->client->path('/api/console/issues/' . $id), null, $options);

        return $this->client->transport->denormalize($result, Issue::class);
    }

    /**
     * DELETE /api/console/issues/{id}
     *
     * @throws HyvorApiException
     */
    public function delete(int $id, ?RequestOptions $options = null): void
    {
        $this->client->request('DELETE', $this->client->path('/api/console/issues/' . $id), null, $options);
    }

    /**
     * PATCH /api/console/issues/{id}
     *
     * @param array{
     *     subject?: string|null,
     *     lists: list<int>|array<string, int>,
     *     content?: string|null,
     *     sending_profile_id: int,
     * } $data
     *
     * @throws HyvorApiException
     */
    public function update(int $id, array $data, ?RequestOptions $options = null): Issue
    {
        $result = $this->client->request('PATCH', $this->client->path('/api/console/issues/' . $id), $data, $options);

        return $this->client->transport->denormalize($result, Issue::class);
    }

    /**
     * POST /api/console/issues/{id}/send
     *
     * @throws HyvorApiException
     */
    public function send(int $id, ?RequestOptions $options = null): Issue
    {
        $result = $this->client->request('POST', $this->client->path('/api/console/issues/' . $id . '/send'), null, $options);

        return $this->client->transport->denormalize($result, Issue::class);
    }

    /**
     * GET /api/console/issues/{id}/test
     *
     * @return array<mixed>
     *
     * @throws HyvorApiException
     */
    public function gettestdata(int $id, ?RequestOptions $options = null): array
    {
        return $this->client->request('GET', $this->client->path('/api/console/issues/' . $id . '/test'), null, $options);
    }

    /**
     * POST /api/console/issues/{id}/test
     *
     * @param array{
     *     emails: list<string>|array<string, string>,
     * } $data
     *
     * @return array<mixed>
     *
     * @throws HyvorApiException
     */
    public function sendtest(int $id, array $data, ?RequestOptions $options = null): array
    {
        return $this->client->request('POST', $this->client->path('/api/console/issues/' . $id . '/test'), $data, $options);
    }

    /**
     * GET /api/console/issues/{id}/preview
     *
     * @return array<mixed>
     *
     * @throws HyvorApiException
     */
    public function preview(int $id, ?RequestOptions $options = null): array
    {
        return $this->client->request('GET', $this->client->path('/api/console/issues/' . $id . '/preview'), null, $options);
    }

    /**
     * GET /api/console/issues/{id}/progress
     *
     * @throws HyvorApiException
     */
    public function getprogress(int $id, ?RequestOptions $options = null): void
    {
        $this->client->request('GET', $this->client->path('/api/console/issues/' . $id . '/progress'), null, $options);
    }

    /**
     * GET /api/console/issues/{id}/sends
     *
     * @return Send[]
     *
     * @throws HyvorApiException
     */
    public function listsends(int $id, ?RequestOptions $options = null): array
    {
        $result = $this->client->request('GET', $this->client->path('/api/console/issues/' . $id . '/sends'), null, $options);

        return $this->client->transport->denormalizeList($result, Send::class);
    }

    /**
     * GET /api/console/issues/{id}/report
     *
     * @return array<mixed>
     *
     * @throws HyvorApiException
     */
    public function getreport(int $id, ?RequestOptions $options = null): array
    {
        return $this->client->request('GET', $this->client->path('/api/console/issues/' . $id . '/report'), null, $options);
    }
}
