import { Org } from './Org.js';
import { Project } from './Project.js';
import { HyvorBaseClient, type HyvorBaseClientOptions } from '@hyvor/sdk-core';

/**
 * The entry point to the Hyvor Relay SDK.
 *
 * ```ts
 * // org-level access, via a cloud API key
 * const client = new RelayClient({ cloudApiKey: '...' });
 * client.org.projects.create(...);
 *
 * // resource-level access, via a per-product API key, no client-level auth needed
 * const client = new RelayClient();
 * const project = client.project(projectId, 'your-product-api-key');
 *
 * // self-hosted: point directly at your own instance instead of *.hyvor.com
 * const client = new RelayClient({ tokenProvider: yourTokenProvider, productUrl: 'https://relay.example.com' });
 * ```
 *
 * See {@link HyvorBaseClient} for the full constructor option docs.
 */
export class RelayClient extends HyvorBaseClient {
    /**
     * Org-level access to Relay resources, accessible via `client.org`.
     */
    readonly org: Org;

    constructor(options: HyvorBaseClientOptions = {}) {
        super('relay', options);
        this.org = new Org(this.transport);
    }

    /**
     * Resource-level access to a single project.
     *
     * @param projectId The project's ID.
     * @param apiKey A resource-level API key scoped to this project. If
     *  omitted, the client's org-level auth is used instead.
     * @param headers Default headers merged into every request made through
     *  the returned client (and its sub-resources). Can be overridden
     *  per-call via `RequestOptions.headers`.
     */
    project(projectId: number | string, apiKey: string | null = null, headers: Record<string, string> = {}): Project {
        return new Project(this.transport, projectId, apiKey, headers);
    }
}
