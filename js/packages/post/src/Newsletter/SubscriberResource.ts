import type { BulkActionSubscriberInput, CreateSubscriberInput, Subscriber } from '../Dto.js';
import type { Newsletter } from '../Newsletter.js';
import type { RequestOptions } from '@hyvor/sdk-core';

/**
 * `client.newsletter(newsletterId).subscriber`
 */
export class SubscriberResource {
    constructor(private readonly client: Newsletter) {
    }

    /**
     * GET /api/console/subscribers
     */
    async list(options?: RequestOptions): Promise<Subscriber[]> {
        const result = await this.client.request('GET', this.client.path('/api/console/subscribers'), null, options);

        return this.client.transport.denormalizeList<Subscriber>(result);
    }

    /**
     * POST /api/console/subscribers
     */
    async create(data: CreateSubscriberInput, options?: RequestOptions): Promise<Subscriber> {
        const result = await this.client.request('POST', this.client.path('/api/console/subscribers'), data, options);

        return this.client.transport.denormalize<Subscriber>(result);
    }

    /**
     * GET /api/console/subscribers/email/{email}
     */
    async getbyemail(email: string, options?: RequestOptions): Promise<Subscriber> {
        const result = await this.client.request('GET', this.client.path(`/api/console/subscribers/email/${encodeURIComponent(email)}`), null, options);

        return this.client.transport.denormalize<Subscriber>(result);
    }

    /**
     * POST /api/console/subscribers/{id}/resend-opt-in
     */
    async resendoptin(id: number, options?: RequestOptions): Promise<void> {
        await this.client.request('POST', this.client.path(`/api/console/subscribers/${id}/resend-opt-in`), null, options);
    }

    /**
     * DELETE /api/console/subscribers/{id}
     */
    async delete(id: number, options?: RequestOptions): Promise<void> {
        await this.client.request('DELETE', this.client.path(`/api/console/subscribers/${id}`), null, options);
    }

    /**
     * POST /api/console/subscribers/bulk
     */
    async bulk(data: BulkActionSubscriberInput, options?: RequestOptions): Promise<unknown> {
        return await this.client.request('POST', this.client.path('/api/console/subscribers/bulk'), data, options);
    }
}
