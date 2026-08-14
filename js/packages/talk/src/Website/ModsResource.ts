import type { CreateModInput } from '../Dto/CreateModInput.js';
import type { DeleteModInput } from '../Dto/DeleteModInput.js';
import type { Mod } from '../Dto/Mod.js';
import type { Website } from '../Website.js';
import type { RequestOptions } from '@hyvor/sdk-core';

/**
 * `client.website(websiteId).mods`
 */
export class ModsResource {
    constructor(private readonly client: Website) {
    }

    /**
     * POST /mods
     */
    async create(data: CreateModInput, options?: RequestOptions): Promise<Mod> {
        const result = await this.client.request('POST', this.client.path('/mods'), data, options);

        return this.client.transport.denormalize<Mod>(result);
    }

    /**
     * DELETE /mods
     */
    async delete(data: DeleteModInput, options?: RequestOptions): Promise<void> {
        await this.client.request('DELETE', this.client.path('/mods'), data, options);
    }
}
