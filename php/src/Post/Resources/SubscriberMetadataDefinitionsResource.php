<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\Post\Dto\SubscriberMetadataDefinition\CreateSubscriberMetadataDefinitionRequest;
use Hyvor\Sdk\Post\Dto\SubscriberMetadataDefinition\SubscriberMetadataDefinition;
use Hyvor\Sdk\Post\Dto\SubscriberMetadataDefinition\UpdateSubscriberMetadataDefinitionRequest;
use Hyvor\Sdk\RequestOptions;

/**
 * `$client->post->newsletter($newsletterId)->subscriberMetadataDefinitions`
 */
final class SubscriberMetadataDefinitionsResource extends NewsletterScopedResource
{
    /**
     * POST /subscriber-metadata-definitions
     *
     * @throws HyvorApiException
     */
    public function create(
        CreateSubscriberMetadataDefinitionRequest $request,
        ?RequestOptions $options = null,
    ): SubscriberMetadataDefinition {
        $body = $this->transport->normalize($request);
        $data = $this->request('POST', $this->path('/subscriber-metadata-definitions'), $body, $options);

        return $this->transport->denormalize($data, SubscriberMetadataDefinition::class);
    }

    /**
     * PATCH /subscriber-metadata-definitions/{id}
     *
     * @throws HyvorApiException
     */
    public function update(
        int $id,
        UpdateSubscriberMetadataDefinitionRequest $request,
        ?RequestOptions $options = null,
    ): SubscriberMetadataDefinition {
        $body = $this->transport->normalize($request);
        $data = $this->request('PATCH', $this->path("/subscriber-metadata-definitions/{$id}"), $body, $options);

        return $this->transport->denormalize($data, SubscriberMetadataDefinition::class);
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
