import type { Domain, DomainCreateInput, DomainIdOrDomainInput } from '../Dto.js';
import type { Project } from '../Project.js';
import type { RequestOptions } from '@hyvor/sdk-core';

/**
 * `client.project(projectId).domains`
 */
export class DomainsResource {
    constructor(private readonly client: Project) {
    }

    /**
     * GET /api/console/domains
     */
    async list(options?: RequestOptions): Promise<Domain[]> {
        const result = await this.client.request('GET', this.client.path('/api/console/domains'), null, options);

        return this.client.transport.denormalizeList<Domain>(result);
    }

    /**
     * POST /api/console/domains
     */
    async create(data: DomainCreateInput, options?: RequestOptions): Promise<Domain> {
        const result = await this.client.request('POST', this.client.path('/api/console/domains'), data, options);

        return this.client.transport.denormalize<Domain>(result);
    }

    /**
     * DELETE /api/console/domains
     */
    async delete(data: DomainIdOrDomainInput, options?: RequestOptions): Promise<void> {
        await this.client.request('DELETE', this.client.path('/api/console/domains'), data, options);
    }

    /**
     * POST /api/console/domains/verify
     */
    async verify(data: DomainIdOrDomainInput, options?: RequestOptions): Promise<Domain> {
        const result = await this.client.request('POST', this.client.path('/api/console/domains/verify'), data, options);

        return this.client.transport.denormalize<Domain>(result);
    }

    /**
     * GET /api/console/domains/by
     */
    async get(data: DomainIdOrDomainInput, options?: RequestOptions): Promise<Domain> {
        const result = await this.client.request('GET', this.client.path('/api/console/domains/by'), data, options);

        return this.client.transport.denormalize<Domain>(result);
    }
}
