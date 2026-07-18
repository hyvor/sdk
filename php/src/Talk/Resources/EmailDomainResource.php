<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\RequestOptions;
use Hyvor\Sdk\Talk\Dto\EmailDomain\CreateEmailDomainRequest;
use Hyvor\Sdk\Talk\Dto\EmailDomain\EmailDomain;
use Hyvor\Sdk\Talk\Dto\EmailDomain\VerifyEmailDomainResponse;

/**
 * `$client->talk->website($websiteId)->emailDomain`
 */
final class EmailDomainResource extends WebsiteScopedResource
{
    /**
     * POST /email/domain
     *
     * @throws HyvorApiException
     */
    public function create(CreateEmailDomainRequest $request, ?RequestOptions $options = null): EmailDomain
    {
        $body = $this->transport->normalize($request);
        $data = $this->request('POST', $this->path('/email/domain'), $body, $options);

        return $this->transport->denormalize($data, EmailDomain::class);
    }

    /**
     * POST /email/domain/verify
     *
     * @throws HyvorApiException
     */
    public function verify(?RequestOptions $options = null): VerifyEmailDomainResponse
    {
        $data = $this->request('POST', $this->path('/email/domain/verify'), null, $options);

        return $this->transport->denormalize($data, VerifyEmailDomainResponse::class);
    }

    /**
     * DELETE /email/domain
     *
     * @throws HyvorApiException
     */
    public function delete(?RequestOptions $options = null): void
    {
        $this->request('DELETE', $this->path('/email/domain'), null, $options);
    }
}
