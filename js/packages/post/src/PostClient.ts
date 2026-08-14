import { Newsletter } from './Newsletter.js';
import { Org } from './Org.js';
import { HyvorBaseClient, type HyvorBaseClientOptions } from '@hyvor/sdk-core';

/**
 * The entry point to the Hyvor Post SDK.
 *
 * ```ts
 * // org-level access, via a cloud API key
 * const client = new PostClient({ cloudApiKey: '...' });
 * client.org.domain.create(...);
 *
 * // resource-level access, via a per-product API key, no client-level auth needed
 * const client = new PostClient();
 * const newsletter = client.newsletter(newsletterId, 'your-product-api-key');
 *
 * // self-hosted: point directly at your own instance instead of *.hyvor.com
 * const client = new PostClient({ tokenProvider: yourTokenProvider, productUrl: 'https://post.example.com' });
 * ```
 *
 * See {@link HyvorBaseClient} for the full constructor option docs.
 */
export class PostClient extends HyvorBaseClient {
    /**
     * Org-level access to Post resources, accessible via `client.org`.
     */
    readonly org: Org;

    constructor(options: HyvorBaseClientOptions = {}) {
        super('post', options);
        this.org = new Org(this.transport);
    }

    /**
     * Resource-level access to a single newsletter.
     *
     * @param newsletterId The newsletter's ID.
     * @param apiKey A resource-level API key scoped to this newsletter. If
     *  omitted, the client's org-level auth is used instead.
     * @param headers Default headers merged into every request made through
     *  the returned client (and its sub-resources). Can be overridden
     *  per-call via `RequestOptions.headers`.
     */
    newsletter(newsletterId: number | string, apiKey: string | null = null, headers: Record<string, string> = {}): Newsletter {
        return new Newsletter(this.transport, newsletterId, apiKey, headers);
    }
}
