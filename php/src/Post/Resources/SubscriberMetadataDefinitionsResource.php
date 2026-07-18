<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Post\Dto\SubscriberMetadataDefinition\SubscriberMetadataDefinition;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->post->newsletter($newsletterId)->subscriberMetadataDefinitions`
 */
final class SubscriberMetadataDefinitionsResource extends NewsletterScopedResource
{
    /**
     * POST /subscriber-metadata-definitions
     *
     * @param array{
     *     key: string,
     *     name: string,
     * } $data key: max length 255, can only contain lowercase letters,
     *  numbers, and underscores, cannot be changed once created. name: max
     *  length 255.
     * @throws HyvorApiException
     */
    public function create(
        array $data,
        ?RequestOptions $options = null,
    ): SubscriberMetadataDefinition {
        $result = $this->request('POST', $this->path('/subscriber-metadata-definitions'), $data, $options);

        return $this->transport->denormalize($result, SubscriberMetadataDefinition::class);
    }

    /**
     * PATCH /subscriber-metadata-definitions/{id}
     *
     * @param array{
     *     name: string,
     * } $data name: max length 255.
     * @throws HyvorApiException
     */
    public function update(
        int $id,
        array $data,
        ?RequestOptions $options = null,
    ): SubscriberMetadataDefinition {
        $result = $this->request('PATCH', $this->path("/subscriber-metadata-definitions/{$id}"), $data, $options);

        return $this->transport->denormalize($result, SubscriberMetadataDefinition::class);
    }

    /**
     * DELETE /subscriber-metadata-definitions/{id}
     *
     * @throws HyvorApiException
     */
    public function delete(int $id, ?RequestOptions $options = null): void
    {
        $this->request('DELETE', $this->path("/subscriber-metadata-definitions/{$id}"), null, $options);
    }
}
