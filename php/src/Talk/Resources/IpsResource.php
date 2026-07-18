<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\RequestOptions;
use Hyvor\Sdk\Talk\Dto\Ip\IP;
use Hyvor\Sdk\Talk\Dto\Ip\ListIpsRequest;
use Hyvor\Sdk\Talk\Dto\Ip\UpdateIpRequest;

/**
 * `$client->talk->website($websiteId)->ips`
 *
 * Only returns IPs that have been banned, shadow-banned, trusted, or have a
 * note added.
 */
final class IpsResource extends WebsiteScopedResource
{
    /**
     * GET /ips
     *
     * @return IP[]
     * @throws HyvorApiException
     */
    public function list(?ListIpsRequest $request = null, ?RequestOptions $options = null): array
    {
        $body = $this->transport->normalize($request ?? new ListIpsRequest());
        $data = $this->request('GET', $this->path('/ips'), $body, $options);

        return $this->transport->denormalizeList($data, IP::class);
    }

    /**
     * GET /ip/{ip}
     *
     * @throws HyvorApiException
     */
    public function get(string $ip, ?RequestOptions $options = null): IP
    {
        $data = $this->request('GET', $this->path('/ip/' . rawurlencode($ip)), null, $options);

        return $this->transport->denormalize($data, IP::class);
    }

    /**
     * PATCH /ip/{ip}
     *
     * @throws HyvorApiException
     */
    public function update(string $ip, UpdateIpRequest $request, ?RequestOptions $options = null): IP
    {
        $body = $this->transport->normalize($request);
        $data = $this->request('PATCH', $this->path('/ip/' . rawurlencode($ip)), $body, $options);

        return $this->transport->denormalize($data, IP::class);
    }
}
