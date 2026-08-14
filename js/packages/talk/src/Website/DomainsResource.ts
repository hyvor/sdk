import type { Domain } from '../Dto/Domain.js';
import type { UpdateDomainsInput } from '../Dto/UpdateDomainsInput.js';
import type { Website } from '../Website.js';
import type { RequestOptions } from '@hyvor/sdk-core';

/**
 * `client.website(websiteId).domains`
 */
export class DomainsResource {
    constructor(private readonly client: Website) {
    }

    /**
     * POST /domains
     */
    async update(data: UpdateDomainsInput, options?: RequestOptions): Promise<Domain[]> {
        const result = await this.client.request('POST', this.client.path('/domains'), data, options);

        return this.client.transport.denormalizeList<Domain>(result);
    }
}
