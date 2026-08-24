import type { ApiKey, CreateApiKeyInput, UpdateApiKeyInput } from '../Dto.js';
import type { Project } from '../Project.js';
import type { RequestOptions } from '@hyvor/sdk-core';

/**
 * `client.project(projectId).apiKeys`
 */
export class ApiKeysResource {
    constructor(private readonly client: Project) {
    }

    /**
     * GET /api/console/api-keys
     */
    async list(options?: RequestOptions): Promise<ApiKey[]> {
        const result = await this.client.request('GET', this.client.path('/api/console/api-keys'), null, options);

        return this.client.transport.denormalizeList<ApiKey>(result);
    }

    /**
     * POST /api/console/api-keys
     */
    async create(data: CreateApiKeyInput, options?: RequestOptions): Promise<ApiKey> {
        const result = await this.client.request('POST', this.client.path('/api/console/api-keys'), data, options);

        return this.client.transport.denormalize<ApiKey>(result);
    }

    /**
     * DELETE /api/console/api-keys/{id}
     */
    async delete(id: number, options?: RequestOptions): Promise<void> {
        await this.client.request('DELETE', this.client.path(`/api/console/api-keys/${id}`), null, options);
    }

    /**
     * PATCH /api/console/api-keys/{id}
     */
    async update(id: number, data: UpdateApiKeyInput, options?: RequestOptions): Promise<ApiKey> {
        const result = await this.client.request('PATCH', this.client.path(`/api/console/api-keys/${id}`), data, options);

        return this.client.transport.denormalize<ApiKey>(result);
    }
}
