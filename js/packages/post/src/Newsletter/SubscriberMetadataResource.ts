import type { CreateSubscriberMetadataDefinitionInput, SubscriberMetadataDefinition, UpdateSubscriberMetadataDefinitionInput } from '../Dto.js';
import type { Newsletter } from '../Newsletter.js';
import type { RequestOptions } from '@hyvor/sdk-core';

/**
 * `client.newsletter(newsletterId).subscriberMetadata`
 */
export class SubscriberMetadataResource {
    constructor(private readonly client: Newsletter) {
    }

    /**
     * POST /api/console/subscriber-metadata-definitions
     */
    async create(data: CreateSubscriberMetadataDefinitionInput, options?: RequestOptions): Promise<SubscriberMetadataDefinition> {
        const result = await this.client.request('POST', this.client.path('/api/console/subscriber-metadata-definitions'), data, options);

        return this.client.transport.denormalize<SubscriberMetadataDefinition>(result);
    }

    /**
     * DELETE /api/console/subscriber-metadata-definitions/{id}
     */
    async delete(id: number, options?: RequestOptions): Promise<void> {
        await this.client.request('DELETE', this.client.path(`/api/console/subscriber-metadata-definitions/${id}`), null, options);
    }

    /**
     * PATCH /api/console/subscriber-metadata-definitions/{id}
     */
    async update(id: number, data: UpdateSubscriberMetadataDefinitionInput, options?: RequestOptions): Promise<SubscriberMetadataDefinition> {
        const result = await this.client.request('PATCH', this.client.path(`/api/console/subscriber-metadata-definitions/${id}`), data, options);

        return this.client.transport.denormalize<SubscriberMetadataDefinition>(result);
    }
}
