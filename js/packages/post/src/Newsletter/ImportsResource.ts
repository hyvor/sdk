import type { ImportInput, SubscriberImport, UploadImportInput } from '../Dto.js';
import type { Newsletter } from '../Newsletter.js';
import type { RequestOptions } from '@hyvor/sdk-core';

/**
 * `client.newsletter(newsletterId).imports`
 */
export class ImportsResource {
    constructor(private readonly client: Newsletter) {
    }

    /**
     * POST /api/console/imports/upload
     */
    async upload(data: UploadImportInput, options?: RequestOptions): Promise<SubscriberImport> {
        const result = await this.client.request('POST', this.client.path('/api/console/imports/upload'), data, options);

        return this.client.transport.denormalize<SubscriberImport>(result);
    }

    /**
     * POST /api/console/imports/{id}
     */
    async start(id: number, data: ImportInput, options?: RequestOptions): Promise<SubscriberImport> {
        const result = await this.client.request('POST', this.client.path(`/api/console/imports/${id}`), data, options);

        return this.client.transport.denormalize<SubscriberImport>(result);
    }

    /**
     * GET /api/console/imports
     */
    async list(options?: RequestOptions): Promise<SubscriberImport[]> {
        const result = await this.client.request('GET', this.client.path('/api/console/imports'), null, options);

        return this.client.transport.denormalizeList<SubscriberImport>(result);
    }

    /**
     * GET /api/console/imports/limits
     */
    async getlimits(options?: RequestOptions): Promise<unknown> {
        return await this.client.request('GET', this.client.path('/api/console/imports/limits'), null, options);
    }
}
