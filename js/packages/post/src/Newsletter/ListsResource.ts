import type { CreateListInput, List, UpdateListInput } from '../Dto.js';
import type { Newsletter } from '../Newsletter.js';
import type { RequestOptions } from '@hyvor/sdk-core';

/**
 * `client.newsletter(newsletterId).lists`
 */
export class ListsResource {
    constructor(private readonly client: Newsletter) {
    }

    /**
     * POST /api/console/lists
     */
    async create(data: CreateListInput, options?: RequestOptions): Promise<List> {
        const result = await this.client.request('POST', this.client.path('/api/console/lists'), data, options);

        return this.client.transport.denormalize<List>(result);
    }

    /**
     * DELETE /api/console/lists/{id}
     */
    async delete(id: number, options?: RequestOptions): Promise<void> {
        await this.client.request('DELETE', this.client.path(`/api/console/lists/${id}`), null, options);
    }

    /**
     * PATCH /api/console/lists/{id}
     */
    async update(id: number, data: UpdateListInput, options?: RequestOptions): Promise<List> {
        const result = await this.client.request('PATCH', this.client.path(`/api/console/lists/${id}`), data, options);

        return this.client.transport.denormalize<List>(result);
    }
}
