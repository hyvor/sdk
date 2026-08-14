import type { CreateDomainInput } from '../Dto/CreateDomainInput.js';
import type { Domain } from '../Dto/Domain.js';
import type { RequestOptions, Transport } from '@hyvor/sdk-core';

/**
 * `client.org.domain`
 */
export class DomainResource {
    constructor(private readonly transport: Transport) {
    }

    /**
     * GET /api/console/domains
     */
    async list(options?: RequestOptions): Promise<Domain[]> {
        const result = await this.transport.request('GET', '/api/console/domains', null, options);

        return this.transport.denormalizeList<Domain>(result);
    }

    /**
     * POST /api/console/domains
     */
    async create(data: CreateDomainInput, options?: RequestOptions): Promise<Domain> {
        const result = await this.transport.request('POST', '/api/console/domains', data, options);

        return this.transport.denormalize<Domain>(result);
    }

    /**
     * POST /api/console/domains/{id}/verify
     */
    async verify(id: number, options?: RequestOptions): Promise<unknown> {
        return await this.transport.request('POST', `/api/console/domains/${id}/verify`, null, options);
    }

    /**
     * DELETE /api/console/domains/{id}
     */
    async delete(id: number, options?: RequestOptions): Promise<void> {
        await this.transport.request('DELETE', `/api/console/domains/${id}`, null, options);
    }
}
