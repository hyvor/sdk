import type { SubscriberExport } from '../Dto.js';
import type { Newsletter } from '../Newsletter.js';
import type { RequestOptions } from '@hyvor/sdk-core';

/**
 * `client.newsletter(newsletterId).export`
 */
export class ExportResource {
    constructor(private readonly client: Newsletter) {
    }

    /**
     * GET /api/console/export
     */
    async list(options?: RequestOptions): Promise<SubscriberExport[]> {
        const result = await this.client.request('GET', this.client.path('/api/console/export'), null, options);

        return this.client.transport.denormalizeList<SubscriberExport>(result);
    }

    /**
     * POST /api/console/export
     */
    async create(options?: RequestOptions): Promise<SubscriberExport> {
        const result = await this.client.request('POST', this.client.path('/api/console/export'), null, options);

        return this.client.transport.denormalize<SubscriberExport>(result);
    }
}
