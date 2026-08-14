import type { Issue } from '../Dto/Issue.js';
import type { Send } from '../Dto/Send.js';
import type { SendTestInput } from '../Dto/SendTestInput.js';
import type { UpdateIssueInput } from '../Dto/UpdateIssueInput.js';
import type { Newsletter } from '../Newsletter.js';
import type { RequestOptions } from '@hyvor/sdk-core';

/**
 * `client.newsletter(newsletterId).issue`
 */
export class IssueResource {
    constructor(private readonly client: Newsletter) {
    }

    /**
     * GET /api/console/issues
     */
    async list(options?: RequestOptions): Promise<Issue[]> {
        const result = await this.client.request('GET', this.client.path('/api/console/issues'), null, options);

        return this.client.transport.denormalizeList<Issue>(result);
    }

    /**
     * POST /api/console/issues
     */
    async create(options?: RequestOptions): Promise<Issue> {
        const result = await this.client.request('POST', this.client.path('/api/console/issues'), null, options);

        return this.client.transport.denormalize<Issue>(result);
    }

    /**
     * GET /api/console/issues/{id}
     */
    async get(id: number, options?: RequestOptions): Promise<Issue> {
        const result = await this.client.request('GET', this.client.path(`/api/console/issues/${id}`), null, options);

        return this.client.transport.denormalize<Issue>(result);
    }

    /**
     * DELETE /api/console/issues/{id}
     */
    async delete(id: number, options?: RequestOptions): Promise<void> {
        await this.client.request('DELETE', this.client.path(`/api/console/issues/${id}`), null, options);
    }

    /**
     * PATCH /api/console/issues/{id}
     */
    async update(id: number, data: UpdateIssueInput, options?: RequestOptions): Promise<Issue> {
        const result = await this.client.request('PATCH', this.client.path(`/api/console/issues/${id}`), data, options);

        return this.client.transport.denormalize<Issue>(result);
    }

    /**
     * POST /api/console/issues/{id}/send
     */
    async send(id: number, options?: RequestOptions): Promise<Issue> {
        const result = await this.client.request('POST', this.client.path(`/api/console/issues/${id}/send`), null, options);

        return this.client.transport.denormalize<Issue>(result);
    }

    /**
     * GET /api/console/issues/{id}/test
     */
    async gettestdata(id: number, options?: RequestOptions): Promise<unknown> {
        return await this.client.request('GET', this.client.path(`/api/console/issues/${id}/test`), null, options);
    }

    /**
     * POST /api/console/issues/{id}/test
     */
    async sendtest(id: number, data: SendTestInput, options?: RequestOptions): Promise<unknown> {
        return await this.client.request('POST', this.client.path(`/api/console/issues/${id}/test`), data, options);
    }

    /**
     * GET /api/console/issues/{id}/preview
     */
    async preview(id: number, options?: RequestOptions): Promise<unknown> {
        return await this.client.request('GET', this.client.path(`/api/console/issues/${id}/preview`), null, options);
    }

    /**
     * GET /api/console/issues/{id}/progress
     */
    async getprogress(id: number, options?: RequestOptions): Promise<void> {
        await this.client.request('GET', this.client.path(`/api/console/issues/${id}/progress`), null, options);
    }

    /**
     * GET /api/console/issues/{id}/sends
     */
    async listsends(id: number, options?: RequestOptions): Promise<Send[]> {
        const result = await this.client.request('GET', this.client.path(`/api/console/issues/${id}/sends`), null, options);

        return this.client.transport.denormalizeList<Send>(result);
    }

    /**
     * GET /api/console/issues/{id}/report
     */
    async getreport(id: number, options?: RequestOptions): Promise<unknown> {
        return await this.client.request('GET', this.client.path(`/api/console/issues/${id}/report`), null, options);
    }
}
