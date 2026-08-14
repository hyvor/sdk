import type { CreateWebsiteInput } from '../Dto/CreateWebsiteInput.js';
import type { RequestOptions, Transport } from '@hyvor/sdk-core';

/**
 * `client.org.websites`
 */
export class WebsitesResource {
    constructor(private readonly transport: Transport) {
    }

    /**
     * POST /api/console/v1/websites
     */
    async create(data: CreateWebsiteInput, options?: RequestOptions): Promise<void> {
        await this.transport.request('POST', '/api/console/v1/websites', data, options);
    }
}
