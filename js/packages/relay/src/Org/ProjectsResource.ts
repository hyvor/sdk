import type { CreateProjectInput, ProjectUser } from '../Dto.js';
import type { RequestOptions, Transport } from '@hyvor/sdk-core';

/**
 * `client.org.projects`
 */
export class ProjectsResource {
    constructor(private readonly transport: Transport) {
    }

    /**
     * POST /api/console/project
     */
    async create(data: CreateProjectInput, options?: RequestOptions): Promise<ProjectUser> {
        const result = await this.transport.request('POST', '/api/console/project', data, options);

        return this.transport.denormalize<ProjectUser>(result);
    }
}
