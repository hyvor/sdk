import type { CreateSendingProfileInput, SendingProfile, UpdateSendingProfileInput } from '../Dto.js';
import type { Newsletter } from '../Newsletter.js';
import type { RequestOptions } from '@hyvor/sdk-core';

/**
 * `client.newsletter(newsletterId).sendingProfiles`
 */
export class SendingProfilesResource {
    constructor(private readonly client: Newsletter) {
    }

    /**
     * GET /api/console/sending-profiles
     */
    async list(options?: RequestOptions): Promise<SendingProfile[]> {
        const result = await this.client.request('GET', this.client.path('/api/console/sending-profiles'), null, options);

        return this.client.transport.denormalizeList<SendingProfile>(result);
    }

    /**
     * POST /api/console/sending-profiles
     */
    async create(data: CreateSendingProfileInput, options?: RequestOptions): Promise<SendingProfile> {
        const result = await this.client.request('POST', this.client.path('/api/console/sending-profiles'), data, options);

        return this.client.transport.denormalize<SendingProfile>(result);
    }

    /**
     * DELETE /api/console/sending-profiles/{id}
     */
    async delete(id: number, options?: RequestOptions): Promise<SendingProfile[]> {
        const result = await this.client.request('DELETE', this.client.path(`/api/console/sending-profiles/${id}`), null, options);

        return this.client.transport.denormalizeList<SendingProfile>(result);
    }

    /**
     * PATCH /api/console/sending-profiles/{id}
     */
    async update(id: number, data: UpdateSendingProfileInput, options?: RequestOptions): Promise<SendingProfile> {
        const result = await this.client.request('PATCH', this.client.path(`/api/console/sending-profiles/${id}`), data, options);

        return this.client.transport.denormalize<SendingProfile>(result);
    }
}
