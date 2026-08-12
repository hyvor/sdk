<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Product\Talk\Dto\EmailLog\EmailLog;
use Hyvor\Sdk\Product\Talk\Dto\EmailLog\ListEmailLogsRequest;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->talk->website($websiteId)->emailLogs`
 */
final class EmailLogsResource extends WebsiteScopedResource
{
    /**
     * GET /email-logs
     *
     * @return EmailLog[]
     * @throws HyvorApiException
     */
    public function list(?ListEmailLogsRequest $request = null, ?RequestOptions $options = null): array
    {
        $body = $this->transport->normalize($request ?? new ListEmailLogsRequest());
        $data = $this->request('GET', $this->path('/email-logs'), $body, $options);

        return $this->transport->denormalizeList($data, EmailLog::class);
    }
}
