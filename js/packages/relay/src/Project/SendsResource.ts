import type { RetrySendInput, Send, SendContent, SendEmailInput } from '../Dto.js';
import type { Project } from '../Project.js';
import type { RequestOptions } from '@hyvor/sdk-core';

/**
 * `client.project(projectId).sends`
 */
export class SendsResource {
    constructor(private readonly client: Project) {
    }

    /**
     * GET /api/console/sends
     */
    async list(options?: RequestOptions): Promise<Send[]> {
        const result = await this.client.request('GET', this.client.path('/api/console/sends'), null, options);

        return this.client.transport.denormalizeList<Send>(result);
    }

    /**
     * POST /api/console/sends
     */
    async send(data: SendEmailInput, options?: RequestOptions): Promise<unknown> {
        return await this.client.request('POST', this.client.path('/api/console/sends'), data, options);
    }

    /**
     * GET /api/console/sends/{id}
     */
    async get(id: number, options?: RequestOptions): Promise<Send> {
        const result = await this.client.request('GET', this.client.path(`/api/console/sends/${id}`), null, options);

        return this.client.transport.denormalize<Send>(result);
    }

    /**
     * POST /api/console/sends/{id}/retry
     */
    async retry(id: number, data: RetrySendInput, options?: RequestOptions): Promise<unknown> {
        return await this.client.request('POST', this.client.path(`/api/console/sends/${id}/retry`), data, options);
    }

    /**
     * GET /api/console/sends/uuid/{uuid}
     */
    async getbyuuid(uuid: string, options?: RequestOptions): Promise<Send> {
        const result = await this.client.request('GET', this.client.path(`/api/console/sends/uuid/${encodeURIComponent(uuid)}`), null, options);

        return this.client.transport.denormalize<Send>(result);
    }

    /**
     * GET /api/console/sends/uuid/{uuid}/content
     */
    async getcontent(uuid: string, options?: RequestOptions): Promise<SendContent> {
        const result = await this.client.request('GET', this.client.path(`/api/console/sends/uuid/${encodeURIComponent(uuid)}/content`), null, options);

        return this.client.transport.denormalize<SendContent>(result);
    }
}
