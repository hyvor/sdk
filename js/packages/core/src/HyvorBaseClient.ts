import { TokenProvider } from './auth.js';
import { HttpClient, Transport, buildTransport } from './http.js';
import { Logger } from './logging.js';

export interface HyvorBaseClientOptions {
    /**
     * A Cloud API key created at https://hyvor.com/account/org/api-keys by
     * an admin of the organization. Scopes are pre-defined. The SDK
     * internally requests a short-lived JWT token from hyvor.com.
     */
    cloudApiKey?: string | null;

    /**
     * A custom token provider.
     */
    tokenProvider?: TokenProvider | null;

    /**
     * Overrides the product URL derived from `cloudInstance`. Set this for a
     * self-hosted instance (ex: `https://talk.example.com`) to bypass
     * `*.hyvor.com` entirely.
     */
    productUrl?: string | null;

    /**
     * A customizable, standard logger interface to use.
     */
    logger?: Logger | null;

    /**
     * A customizable, standard HTTP client interface to use.
     */
    httpClient?: HttpClient | null;

    /**
     * The maximum number of attempts (including the first) before giving up.
     * @default 3
     */
    retryMaxAttempts?: number;

    /**
     * The multiplier applied to the base delay between each retry
     * (exponential backoff).
     * @default 2.0
     */
    retryBackoffFactor?: number;

    /**
     * Can be used to set a custom cloud instance. Only relevant for
     * hyvor.com-hosted (cloud) usage - end users essentially never set this;
     * self-hosted users should set `productUrl` instead.
     * @default "https://hyvor.com"
     */
    cloudInstance?: string;
}

/**
 * Base class for every product's entry-point client (`TalkClient`,
 * `PostClient`, ...). Builds the shared `Transport` from the friendly
 * options every product client accepts, so that wiring lives in one place
 * instead of being duplicated per product.
 *
 * If `httpClient` is not given, requests are sent via the global `fetch`
 * (available natively in Node >= 18, browsers, and other modern runtimes).
 *
 * @internal Depend on the concrete product client (ex: `TalkClient`), not
 *  this class.
 */
export abstract class HyvorBaseClient {

    readonly transport: Transport;

    constructor(product: string, options: HyvorBaseClientOptions = {}) {
        this.transport = buildTransport({ product, ...options });
    }

}
