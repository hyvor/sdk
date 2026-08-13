<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Website;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Product\Talk\Dto\Moderator\ModObject;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->talk->website($websiteId)->moderators`
 */
final class ModeratorsResource extends WebsiteScopedResource
{
    /**
     * POST /mods
     *
     * @param array{
     *     user_id: int,
     *     role?: 'mod'|'admin',
     *     on_duplicate?: 'throw'|'ignore',
     * } $data
     * @throws HyvorApiException
     */
    public function create(array $data, ?RequestOptions $options = null): ModObject
    {
        $result = $this->request('POST', $this->path('/mods'), $data, $options);

        return $this->transport->denormalize($result, ModObject::class);
    }

    /**
     * DELETE /mods
     *
     * @param array{
     *     user_id?: int|null,
     *     mod_id?: int|null,
     * } $data
     * @throws HyvorApiException
     */
    public function delete(array $data, ?RequestOptions $options = null): void
    {
        $this->request('DELETE', $this->path('/mods'), $data, $options);
    }
}
