import { DomainsResource } from './Website/DomainsResource.js';
import { ModsResource } from './Website/ModsResource.js';
import type { RequestOptions, Transport } from '@hyvor/sdk-core';

/**
 * Resource-level access to a single website, accessible via
 * `client.website(websiteId)`.
 *
 * Authenticated either with the client's org-level auth (a cloud API
 * key or token provider, which must have access to this website), or
 * with a resource-level API key, passed as `apiKey`.
 */
export class Website {
    readonly domains: DomainsResource;
    readonly mods: ModsResource;

    constructor(
        readonly transport: Transport,
        private readonly websiteId: number | string,
        private readonly apiKey: string | null = null,
        private readonly headers: Record<string, string> = {},
    ) {
        this.domains = new DomainsResource(this);
        this.mods = new ModsResource(this);
    }

    path(suffix: string = ''): string {
        return `/api/console/v1/${this.websiteId}${suffix}`;
    }

    async request(method: string, path: string, jsonBody: unknown = null, options?: RequestOptions): Promise<unknown> {
        return this.transport.request(method, path, jsonBody, options, this.apiKey, this.headers);
    }

    /**
     * DELETE /website
     */
    async delete(options?: RequestOptions): Promise<void> {
        await this.request('DELETE', this.path('/website'), null, options);
    }
}
