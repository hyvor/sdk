import type { CreateWebsiteInput, Website } from '../Dto.js';
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
    async create(data: CreateWebsiteInput, options?: RequestOptions): Promise<Website> {
        const result = await this.transport.request('POST', '/api/console/v1/websites', data, options);

        return this.transport.denormalize<Website>(result);
    }
}
