<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Website;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\RequestOptions;
use Hyvor\Sdk\Talk\Dto\Mod;
use Hyvor\Sdk\Talk\Website;

/**
 * `$client->website($websiteId)->mods`
 */
final class ModsResource
{
    public function __construct(private readonly Website $client)
    {
    }

    /**
     * POST /mods
     *
     * @param array{
     *     user_id: int,
     *     role?: 'mod'|'admin',
     *     on_duplicate?: 'throw'|'ignore',
     * } $data
     *
     * @throws HyvorApiException
     */
    public function create(array $data, ?RequestOptions $options = null): Mod
    {
        $result = $this->client->request('POST', $this->client->path('/mods'), $data, $options);

        return $this->client->transport->denormalize($result, Mod::class);
    }

    /**
     * DELETE /mods
     *
     * @param array{
     *     user_id?: int|null,
     *     mod_id?: int|null,
     * } $data
     *
     * @throws HyvorApiException
     */
    public function delete(array $data, ?RequestOptions $options = null): void
    {
        $this->client->request('DELETE', $this->client->path('/mods'), $data, $options);
    }
}
