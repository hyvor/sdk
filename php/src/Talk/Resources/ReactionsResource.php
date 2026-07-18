<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\RequestOptions;
use Hyvor\Sdk\Talk\Dto\Reaction\ListReactionsRequest;
use Hyvor\Sdk\Talk\Dto\Reaction\Reaction;

/**
 * `$client->talk->website($websiteId)->reactions`
 */
final class ReactionsResource extends WebsiteScopedResource
{
    /**
     * GET /reactions
     *
     * @return Reaction[]
     * @throws HyvorApiException
     */
    public function list(?ListReactionsRequest $request = null, ?RequestOptions $options = null): array
    {
        $body = $this->transport->normalize($request ?? new ListReactionsRequest());
        $data = $this->request('GET', $this->path('/reactions'), $body, $options);

        return $this->transport->denormalizeList($data, Reaction::class);
    }

    /**
     * DELETE /reaction/{id}
     *
     * @throws HyvorApiException
     */
    public function delete(int $id, ?RequestOptions $options = null): void
    {
        $this->request('DELETE', $this->path("/reaction/{$id}"), null, $options);
    }
}
