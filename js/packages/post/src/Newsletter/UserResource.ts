import type { CreateUserInput, DeleteUserInput, User } from '../Dto.js';
import type { Newsletter } from '../Newsletter.js';
import type { RequestOptions } from '@hyvor/sdk-core';

/**
 * `client.newsletter(newsletterId).user`
 */
export class UserResource {
    constructor(private readonly client: Newsletter) {
    }

    /**
     * GET /api/console/users
     */
    async list(options?: RequestOptions): Promise<User[]> {
        const result = await this.client.request('GET', this.client.path('/api/console/users'), null, options);

        return this.client.transport.denormalizeList<User>(result);
    }

    /**
     * POST /api/console/users
     */
    async create(data: CreateUserInput, options?: RequestOptions): Promise<User> {
        const result = await this.client.request('POST', this.client.path('/api/console/users'), data, options);

        return this.client.transport.denormalize<User>(result);
    }

    /**
     * DELETE /api/console/users
     */
    async delete(data: DeleteUserInput, options?: RequestOptions): Promise<void> {
        await this.client.request('DELETE', this.client.path('/api/console/users'), data, options);
    }
}
