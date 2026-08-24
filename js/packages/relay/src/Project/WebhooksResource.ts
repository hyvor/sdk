import type { CreateWebhookInput, UpdateWebhookInput, Webhook, WebhookDelivery } from '../Dto.js';
import type { Project } from '../Project.js';
import type { RequestOptions } from '@hyvor/sdk-core';

/**
 * `client.project(projectId).webhooks`
 */
export class WebhooksResource {
    constructor(private readonly client: Project) {
    }

    /**
     * GET /api/console/webhooks
     */
    async list(options?: RequestOptions): Promise<Webhook[]> {
        const result = await this.client.request('GET', this.client.path('/api/console/webhooks'), null, options);

        return this.client.transport.denormalizeList<Webhook>(result);
    }

    /**
     * POST /api/console/webhooks
     */
    async create(data: CreateWebhookInput, options?: RequestOptions): Promise<Webhook> {
        const result = await this.client.request('POST', this.client.path('/api/console/webhooks'), data, options);

        return this.client.transport.denormalize<Webhook>(result);
    }

    /**
     * DELETE /api/console/webhooks/{id}
     */
    async delete(id: number, options?: RequestOptions): Promise<void> {
        await this.client.request('DELETE', this.client.path(`/api/console/webhooks/${id}`), null, options);
    }

    /**
     * PATCH /api/console/webhooks/{id}
     */
    async update(id: number, data: UpdateWebhookInput, options?: RequestOptions): Promise<Webhook> {
        const result = await this.client.request('PATCH', this.client.path(`/api/console/webhooks/${id}`), data, options);

        return this.client.transport.denormalize<Webhook>(result);
    }

    /**
     * GET /api/console/webhooks/deliveries
     */
    async listdeliveries(options?: RequestOptions): Promise<WebhookDelivery[]> {
        const result = await this.client.request('GET', this.client.path('/api/console/webhooks/deliveries'), null, options);

        return this.client.transport.denormalizeList<WebhookDelivery>(result);
    }
}
