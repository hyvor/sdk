import type { Project as Project2, UpdateProjectInput } from './Dto.js';
import { AnalyticsResource } from './Project/AnalyticsResource.js';
import { ApiKeysResource } from './Project/ApiKeysResource.js';
import { DomainsResource } from './Project/DomainsResource.js';
import { ProjectUsersResource } from './Project/ProjectUsersResource.js';
import { SendsResource } from './Project/SendsResource.js';
import { SuppressionsResource } from './Project/SuppressionsResource.js';
import { WebhooksResource } from './Project/WebhooksResource.js';
import type { RequestOptions, Transport } from '@hyvor/sdk-core';

/**
 * Resource-level access to a single project, accessible via
 * `client.project(projectId)`.
 *
 * Authenticated either with the client's org-level auth (a cloud API
 * key or token provider, which must have access to this project), or
 * with a resource-level API key, passed as `apiKey`.
 */
export class Project {
    readonly analytics: AnalyticsResource;
    readonly apiKeys: ApiKeysResource;
    readonly domains: DomainsResource;
    readonly projectUsers: ProjectUsersResource;
    readonly sends: SendsResource;
    readonly suppressions: SuppressionsResource;
    readonly webhooks: WebhooksResource;
    private readonly resourceHeaders: Record<string, string>;

    constructor(
        readonly transport: Transport,
        private readonly projectId: number | string,
        private readonly apiKey: string | null = null,
        private readonly headers: Record<string, string> = {},
    ) {
        this.resourceHeaders = { 'X-Project-Id': String(projectId), ...headers };

        this.analytics = new AnalyticsResource(this);
        this.apiKeys = new ApiKeysResource(this);
        this.domains = new DomainsResource(this);
        this.projectUsers = new ProjectUsersResource(this);
        this.sends = new SendsResource(this);
        this.suppressions = new SuppressionsResource(this);
        this.webhooks = new WebhooksResource(this);
    }

    path(suffix: string = ''): string {
        return suffix;
    }

    async request(method: string, path: string, jsonBody: unknown = null, options?: RequestOptions): Promise<unknown> {
        return this.transport.request(method, path, jsonBody, options, this.apiKey, this.resourceHeaders);
    }

    /**
     * GET /api/console/project
     */
    async get(options?: RequestOptions): Promise<Project2> {
        const result = await this.request('GET', this.path('/api/console/project'), null, options);

        return this.transport.denormalize<Project2>(result);
    }

    /**
     * PATCH /api/console/project
     */
    async update(data: UpdateProjectInput, options?: RequestOptions): Promise<Project2> {
        const result = await this.request('PATCH', this.path('/api/console/project'), data, options);

        return this.transport.denormalize<Project2>(result);
    }
}
