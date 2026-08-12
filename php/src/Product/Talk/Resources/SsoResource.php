<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Product\Talk\Dto\Sso\DeleteSsoUserRequest;
use Hyvor\Sdk\Product\Talk\Dto\Sso\ListSsoUsersRequest;
use Hyvor\Sdk\Product\Talk\Dto\Sso\UpsertSsoUserRequest;
use Hyvor\Sdk\Product\Talk\Dto\User\LoggedInUser;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->talk->website($websiteId)->sso`
 */
final class SsoResource extends WebsiteScopedResource
{
    /**
     * GET /sso/users
     *
     * @return LoggedInUser[]
     * @throws HyvorApiException
     */
    public function list(?ListSsoUsersRequest $request = null, ?RequestOptions $options = null): array
    {
        $body = $this->transport->normalize($request ?? new ListSsoUsersRequest());
        $data = $this->request('GET', $this->path('/sso/users'), $body, $options);

        return $this->transport->denormalizeList($data, LoggedInUser::class);
    }

    /**
     * POST /sso/user
     *
     * @throws HyvorApiException
     */
    public function createOrUpdate(UpsertSsoUserRequest $request, ?RequestOptions $options = null): LoggedInUser
    {
        $body = $this->transport->normalize($request);
        $data = $this->request('POST', $this->path('/sso/user'), $body, $options);

        return $this->transport->denormalize($data, LoggedInUser::class);
    }

    /**
     * DELETE /sso/user
     *
     * @throws HyvorApiException
     */
    public function delete(DeleteSsoUserRequest $request, ?RequestOptions $options = null): void
    {
        $body = $this->transport->normalize($request);
        $this->request('DELETE', $this->path('/sso/user'), $body, $options);
    }
}
