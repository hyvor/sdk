<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Relay\Project;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Relay\Dto\Send;
use Hyvor\Sdk\Relay\Dto\SendContent;
use Hyvor\Sdk\Relay\Project;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->project($projectId)->sends`
 */
final class SendsResource
{
    public function __construct(private readonly Project $client)
    {
    }

    /**
     * GET /api/console/sends
     *
     * @return Send[]
     *
     * @throws HyvorApiException
     */
    public function list(?RequestOptions $options = null): array
    {
        $result = $this->client->request('GET', $this->client->path('/api/console/sends'), null, $options);

        return $this->client->transport->denormalizeList($result, Send::class);
    }

    /**
     * POST /api/console/sends
     *
     * @param array{
     *     from: array<string, string>|string,
     *     to: list<array<string, string>|string>|array<string, array<string, string>|string>|array<string, string>|string,
     *     cc?: list<array<string, string>|string>|array<string, array<string, string>|string>|array<string, string>|string,
     *     bcc?: list<array<string, string>|string>|array<string, array<string, string>|string>|array<string, string>|string,
     *     subject?: string,
     *     body_html?: string|null,
     *     body_text?: string|null,
     *     headers?: array<string, string>,
     *     attachments?: list<array<string, string|null>>,
     * } $data
     *
     * @return array<mixed>
     *
     * @throws HyvorApiException
     */
    public function send(array $data, ?RequestOptions $options = null): array
    {
        return $this->client->request('POST', $this->client->path('/api/console/sends'), $data, $options);
    }

    /**
     * GET /api/console/sends/{id}
     *
     * @throws HyvorApiException
     */
    public function get(int $id, ?RequestOptions $options = null): Send
    {
        $result = $this->client->request('GET', $this->client->path('/api/console/sends/' . $id), null, $options);

        return $this->client->transport->denormalize($result, Send::class);
    }

    /**
     * POST /api/console/sends/{id}/retry
     *
     * @param array{
     *     send_after?: int|null,
     *     recipient_ids?: list<int>|null,
     * } $data
     *
     * @return array<mixed>
     *
     * @throws HyvorApiException
     */
    public function retry(int $id, array $data, ?RequestOptions $options = null): array
    {
        return $this->client->request('POST', $this->client->path('/api/console/sends/' . $id . '/retry'), $data, $options);
    }

    /**
     * GET /api/console/sends/uuid/{uuid}
     *
     * @throws HyvorApiException
     */
    public function getbyuuid(string $uuid, ?RequestOptions $options = null): Send
    {
        $result = $this->client->request('GET', $this->client->path('/api/console/sends/uuid/' . rawurlencode($uuid)), null, $options);

        return $this->client->transport->denormalize($result, Send::class);
    }

    /**
     * GET /api/console/sends/uuid/{uuid}/content
     *
     * @throws HyvorApiException
     */
    public function getcontent(string $uuid, ?RequestOptions $options = null): SendContent
    {
        $result = $this->client->request('GET', $this->client->path('/api/console/sends/uuid/' . rawurlencode($uuid) . '/content'), null, $options);

        return $this->client->transport->denormalize($result, SendContent::class);
    }
}
