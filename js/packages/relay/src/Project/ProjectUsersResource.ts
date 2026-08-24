import type { CreateProjectUserInput, ProjectUser } from '../Dto.js';
import type { Project } from '../Project.js';
import type { RequestOptions } from '@hyvor/sdk-core';

/**
 * `client.project(projectId).projectUsers`
 */
export class ProjectUsersResource {
    constructor(private readonly client: Project) {
    }

    /**
     * GET /api/console/project-users
     */
    async list(options?: RequestOptions): Promise<ProjectUser[]> {
        const result = await this.client.request('GET', this.client.path('/api/console/project-users'), null, options);

        return this.client.transport.denormalizeList<ProjectUser>(result);
    }

    /**
     * POST /api/console/project-users
     */
    async create(data: CreateProjectUserInput, options?: RequestOptions): Promise<ProjectUser> {
        const result = await this.client.request('POST', this.client.path('/api/console/project-users'), data, options);

        return this.client.transport.denormalize<ProjectUser>(result);
    }

    /**
     * DELETE /api/console/project-users
     */
    async deleteall(options?: RequestOptions): Promise<void> {
        await this.client.request('DELETE', this.client.path('/api/console/project-users'), null, options);
    }

    /**
     * DELETE /api/console/project-users/{id}
     */
    async delete(id: number, options?: RequestOptions): Promise<void> {
        await this.client.request('DELETE', this.client.path(`/api/console/project-users/${id}`), null, options);
    }
}
