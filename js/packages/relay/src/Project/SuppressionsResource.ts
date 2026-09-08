import type { Suppression } from '../Dto.js';
import type { Project } from '../Project.js';
import type { RequestOptions } from '@hyvor/sdk-core';

/**
 * `client.project(projectId).suppressions`
 */
export class SuppressionsResource {
    constructor(private readonly client: Project) {
    }

    /**
     * GET /api/console/suppressions
     */
    async list(options?: RequestOptions): Promise<Suppression[]> {
        const result = await this.client.request('GET', this.client.path('/api/console/suppressions'), null, options);

        return this.client.transport.denormalizeList<Suppression>(result);
    }

    /**
     * DELETE /api/console/suppressions/{id}
     */
    async delete(id: number, options?: RequestOptions): Promise<void> {
        await this.client.request('DELETE', this.client.path(`/api/console/suppressions/${id}`), null, options);
    }
}
