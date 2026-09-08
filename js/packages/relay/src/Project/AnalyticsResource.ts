import type { Project } from '../Project.js';
import type { RequestOptions } from '@hyvor/sdk-core';

/**
 * `client.project(projectId).analytics`
 */
export class AnalyticsResource {
    constructor(private readonly client: Project) {
    }

    /**
     * GET /api/console/analytics/stats
     */
    async stats(options?: RequestOptions): Promise<unknown> {
        return await this.client.request('GET', this.client.path('/api/console/analytics/stats'), null, options);
    }

    /**
     * GET /api/console/analytics/sends/chart
     */
    async sendschart(options?: RequestOptions): Promise<unknown> {
        return await this.client.request('GET', this.client.path('/api/console/analytics/sends/chart'), null, options);
    }
}
