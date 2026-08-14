import type { CreateNewsletterInput } from '../Dto/CreateNewsletterInput.js';
import type { Newsletter } from '../Dto/Newsletter.js';
import type { RequestOptions, Transport } from '@hyvor/sdk-core';

/**
 * `client.org.newsletters`
 */
export class NewslettersResource {
    constructor(private readonly transport: Transport) {
    }

    /**
     * POST /api/console/newsletters
     */
    async create(data: CreateNewsletterInput, options?: RequestOptions): Promise<Newsletter> {
        const result = await this.transport.request('POST', '/api/console/newsletters', data, options);

        return this.transport.denormalize<Newsletter>(result);
    }
}
