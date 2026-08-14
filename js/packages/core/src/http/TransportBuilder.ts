import { CloudApiKeyTokenProvider } from '../auth/CloudApiKeyTokenProvider.js';
import { TokenProvider } from '../auth/TokenProvider.js';
import { FetchHttpClient, HttpClient } from './HttpClient.js';
import { Logger, NullLogger } from '../logging/Logger.js';
import { VERSION } from '../version.js';
import { resolveProductBaseUrl } from './ProductBaseUrl.js';
import { Transport } from './Transport.js';

export interface TransportBuilderOptions {
    product: string;
    cloudApiKey?: string | null;
    tokenProvider?: TokenProvider | null;
    productUrl?: string | null;
    logger?: Logger | null;
    httpClient?: HttpClient | null;
    retryMaxAttempts?: number;
    retryBackoffFactor?: number;
    cloudInstance?: string;
}

/**
 * Builds a product-scoped {@link Transport} from the friendly options every
 * product client accepts (cloudApiKey, httpClient, ...). Each product
 * client's constructor delegates here (via `HyvorBaseClient`) so the
 * discovery/auth wiring lives in one place instead of being duplicated per
 * product.
 *
 * @internal
 */
export function buildTransport(options: TransportBuilderOptions): Transport {
    if (options.cloudApiKey != null && options.tokenProvider != null) {
        throw new Error('Provide either cloudApiKey or tokenProvider, not both.');
    }

    const logger = options.logger ?? new NullLogger();
    const httpClient = options.httpClient ?? new FetchHttpClient();
    const cloudInstance = options.cloudInstance ?? 'https://hyvor.com';

    let tokenProvider = options.tokenProvider ?? null;
    if (tokenProvider === null && options.cloudApiKey) {
        tokenProvider = new CloudApiKeyTokenProvider(options.cloudApiKey, cloudInstance, httpClient, logger);
    }

    return new Transport({
        httpClient,
        logger,
        tokenProvider,
        baseUrl: options.productUrl ?? resolveProductBaseUrl(cloudInstance, options.product),
        defaultRetryMaxAttempts: options.retryMaxAttempts ?? 3,
        defaultRetryBackoffFactor: options.retryBackoffFactor ?? 2.0,
        userAgent: `hyvor/sdk-js-${options.product}/${VERSION}`,
    });
}
