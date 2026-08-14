import type { RenderTemplateInput, Template, UpdateTemplateInput } from '../Dto.js';
import type { Newsletter } from '../Newsletter.js';
import type { RequestOptions } from '@hyvor/sdk-core';

/**
 * `client.newsletter(newsletterId).templates`
 */
export class TemplatesResource {
    constructor(private readonly client: Newsletter) {
    }

    /**
     * GET /api/console/templates
     */
    async get(options?: RequestOptions): Promise<Template> {
        const result = await this.client.request('GET', this.client.path('/api/console/templates'), null, options);

        return this.client.transport.denormalize<Template>(result);
    }

    /**
     * PATCH /api/console/templates
     */
    async update(data: UpdateTemplateInput, options?: RequestOptions): Promise<Template> {
        const result = await this.client.request('PATCH', this.client.path('/api/console/templates'), data, options);

        return this.client.transport.denormalize<Template>(result);
    }

    /**
     * POST /api/console/templates/render
     */
    async preview(data: RenderTemplateInput, options?: RequestOptions): Promise<unknown> {
        return await this.client.request('POST', this.client.path('/api/console/templates/render'), data, options);
    }
}
