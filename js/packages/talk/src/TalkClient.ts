import { Org } from './Org.js';
import { Website } from './Website.js';
import { HyvorBaseClient, type HyvorBaseClientOptions } from '@hyvor/sdk-core';

/**
 * The entry point to the Hyvor Talk SDK.
 *
 * ```ts
 * // org-level access, via a cloud API key
 * const client = new TalkClient({ cloudApiKey: '...' });
 * client.org.websites.create(...);
 *
 * // resource-level access, via a per-product API key, no client-level auth needed
 * const client = new TalkClient();
 * const website = client.website(websiteId, 'your-product-api-key');
 *
 * // self-hosted: point directly at your own instance instead of *.hyvor.com
 * const client = new TalkClient({ tokenProvider: yourTokenProvider, productUrl: 'https://talk.example.com' });
 * ```
 *
 * See {@link HyvorBaseClient} for the full constructor option docs.
 */
export class TalkClient extends HyvorBaseClient {
    /**
     * Org-level access to Talk resources, accessible via `client.org`.
     */
    readonly org: Org;

    constructor(options: HyvorBaseClientOptions = {}) {
        super('talk', options);
        this.org = new Org(this.transport);
    }

    /**
     * Resource-level access to a single website.
     *
     * @param websiteId The website's ID.
     * @param apiKey A resource-level API key scoped to this website. If
     *  omitted, the client's org-level auth is used instead.
     * @param headers Default headers merged into every request made through
     *  the returned client (and its sub-resources). Can be overridden
     *  per-call via `RequestOptions.headers`.
     */
    website(websiteId: number | string, apiKey: string | null = null, headers: Record<string, string> = {}): Website {
        return new Website(this.transport, websiteId, apiKey, headers);
    }
}
