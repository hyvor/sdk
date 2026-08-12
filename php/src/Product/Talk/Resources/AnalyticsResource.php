<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Product\Talk\Dto\Analytics\CommentAnalyticsPoint;
use Hyvor\Sdk\Product\Talk\Dto\Analytics\CommentAnalyticsRequest;
use Hyvor\Sdk\Product\Talk\Dto\Analytics\CreditAnalyticsPoint;
use Hyvor\Sdk\Product\Talk\Dto\Analytics\CreditAnalyticsRequest;
use Hyvor\Sdk\Product\Talk\Dto\Analytics\WebsiteStats;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->talk->website($websiteId)->analytics`
 */
final class AnalyticsResource extends WebsiteScopedResource
{
    /**
     * GET /analytics/stats
     *
     * @throws HyvorApiException
     */
    public function stats(?RequestOptions $options = null): WebsiteStats
    {
        $data = $this->request('GET', $this->path('/analytics/stats'), null, $options);

        return $this->transport->denormalize($data, WebsiteStats::class);
    }

    /**
     * GET /analytics/credits
     *
     * @return CreditAnalyticsPoint[]
     * @throws HyvorApiException
     */
    public function credits(CreditAnalyticsRequest $request, ?RequestOptions $options = null): array
    {
        $body = $this->transport->normalize($request);
        $data = $this->request('GET', $this->path('/analytics/credits'), $body, $options);

        return $this->transport->denormalizeList($data, CreditAnalyticsPoint::class);
    }

    /**
     * GET /analytics/comments
     *
     * @return CommentAnalyticsPoint[]
     * @throws HyvorApiException
     */
    public function comments(CommentAnalyticsRequest $request, ?RequestOptions $options = null): array
    {
        $body = $this->transport->normalize($request);
        $data = $this->request('GET', $this->path('/analytics/comments'), $body, $options);

        return $this->transport->denormalizeList($data, CommentAnalyticsPoint::class);
    }
}
