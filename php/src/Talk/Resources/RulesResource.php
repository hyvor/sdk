<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\RequestOptions;
use Hyvor\Sdk\Talk\Dto\Rule\Rule;
use Hyvor\Sdk\Talk\Dto\Rule\UpsertRuleRequest;

/**
 * `$client->talk->website($websiteId)->rules`
 */
final class RulesResource extends WebsiteScopedResource
{
    /**
     * GET /rules
     *
     * @return Rule[]
     * @throws HyvorApiException
     */
    public function list(?RequestOptions $options = null): array
    {
        $data = $this->request('GET', $this->path('/rules'), null, $options);

        return $this->transport->denormalizeList($data, Rule::class);
    }

    /**
     * POST /rule
     *
     * @throws HyvorApiException
     */
    public function create(UpsertRuleRequest $request, ?RequestOptions $options = null): Rule
    {
        $body = $this->transport->normalize($request);
        $data = $this->request('POST', $this->path('/rule'), $body, $options);

        return $this->transport->denormalize($data, Rule::class);
    }

    /**
     * PATCH /rule/{id}
     *
     * @throws HyvorApiException
     */
    public function update(int $id, UpsertRuleRequest $request, ?RequestOptions $options = null): Rule
    {
        $body = $this->transport->normalize($request);
        $data = $this->request('PATCH', $this->path("/rule/{$id}"), $body, $options);

        return $this->transport->denormalize($data, Rule::class);
    }

    /**
     * DELETE /rule/{id}
     *
     * @throws HyvorApiException
     */
    public function delete(int $id, ?RequestOptions $options = null): void
    {
        $this->request('DELETE', $this->path("/rule/{$id}"), null, $options);
    }
}
