<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Post\Dto\Issue\Issue;
use Hyvor\Sdk\Post\Dto\Issue\Send;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->post->newsletter($newsletterId)->issues`
 */
final class IssuesResource extends NewsletterScopedResource
{
    /**
     * GET /issues
     *
     * @param array{
     *     limit?: int,
     *     offset?: int,
     * } $data
     * @return Issue[]
     * @throws HyvorApiException
     */
    public function list(array $data = [], ?RequestOptions $options = null): array
    {
        $result = $this->request('GET', $this->path('/issues'), $data, $options);

        return $this->transport->denormalizeList($result, Issue::class);
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
     * Every key is optional; keys left out of `$data` are left unchanged.
     *
     * @param array{
     *     subject?: string,
     *     lists?: int[],
     *     content?: string,
     *     sending_profile_id?: int,
     * } $data
     * @throws HyvorApiException
     */
    public function update(int $id, array $data, ?RequestOptions $options = null): Issue
    {
        $result = $this->request('PATCH', $this->path("/issues/{$id}"), $data, $options);

        return $this->transport->denormalize($result, Issue::class);
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
