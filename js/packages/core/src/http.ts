import { CloudApiKeyTokenProvider, TokenProvider } from './auth.js';
import {
    ApiException,
    AuthenticationException,
    HyvorApiException,
    NetworkException,
    NotFoundException,
    RateLimitException,
    ServerErrorException,
    ValidationFailedException,
} from './exceptions.js';
import { Logger, NullLogger } from './logging.js';
import { RequestOptions } from './RequestOptions.js';
import { VERSION } from './version.js';

const BASE_RETRY_DELAY_MS = 200;

// --- HTTP client -----------------------------------------------------------

/**
 * A single HTTP request to send. Deliberately narrower than the DOM
 * `Request`/`fetch()` signature (a plain string body, a plain header map) so
 * that implementing {@link HttpClient} against a non-fetch stack (ex: Node's
 * `http`, a test double) doesn't require constructing DOM types.
 */
export interface HttpRequest {
    method: string;
    url: string;
    headers: Record<string, string>;
    body?: string;
}

export interface HttpResponse {
    status: number;
    /** Header names are lower-cased, matching the Fetch `Headers` iteration order/casing. */
    headers: Record<string, string>;
    body: string;
}

/**
 * A customizable, standard HTTP client interface. Implement this to plug in
 * your own HTTP stack, or to mock/record requests in tests (see
 * `FakeHttpClient` in `testing.ts`).
 */
export interface HttpClient {
    send(request: HttpRequest): Promise<HttpResponse>;
}

/**
 * The default {@link HttpClient}: a thin wrapper around the global `fetch`
 * (available natively in Node >= 18, browsers, and other modern runtimes).
 */
export class FetchHttpClient implements HttpClient {
    async send(request: HttpRequest): Promise<HttpResponse> {
        const response = await fetch(request.url, {
            method: request.method,
            headers: request.headers,
            body: request.body,
        });

        const headers: Record<string, string> = {};
        response.headers.forEach((value, key) => {
            headers[key] = value;
        });

        return {
            status: response.status,
            headers,
            body: await response.text(),
        };
    }
}

// --- Error mapping -----------------------------------------------------------

/**
 * @internal
 */
export function mapErrorResponse(response: HttpResponse, requestMethod: string, requestUrl: string): HyvorApiException {
    const statusCode = response.status;
    const body = decodeBody(response.body);

    const message = isRecord(body) && typeof body.message === 'string'
        ? body.message
        : `${requestMethod} ${requestUrl} returned HTTP ${statusCode}`;

    if (statusCode === 422) {
        return new ValidationFailedException(message, normalizeValidationErrors(body), body);
    }
    if (statusCode === 429) {
        return new RateLimitException(message, retryAfterSeconds(response), body);
    }
    if (statusCode === 401 || statusCode === 403) {
        return new AuthenticationException(message, statusCode, body);
    }
    if (statusCode === 404) {
        return new NotFoundException(message, 404, body);
    }
    if (statusCode >= 500) {
        return new ServerErrorException(message, statusCode, body);
    }

    return new ApiException(message, statusCode, body);
}

/**
 * @internal
 */
export function isRetryable(statusCode: number): boolean {
    return statusCode === 429 || statusCode >= 500;
}

function isRecord(value: unknown): value is Record<string, unknown> {
    return typeof value === 'object' && value !== null && !Array.isArray(value);
}

function decodeBody(raw: string): unknown {
    if (raw === '') {
        return null;
    }

    try {
        return JSON.parse(raw);
    } catch {
        return null;
    }
}

function retryAfterSeconds(response: HttpResponse): number | null {
    const header = response.headers['retry-after'];
    return header !== undefined && header !== '' && !isNaN(Number(header)) ? Number(header) : null;
}

function normalizeValidationErrors(body: unknown): Record<string, string[]> {
    if (!isRecord(body) || !isRecord(body.errors)) {
        return {};
    }

    const normalized: Record<string, string[]> = {};

    for (const [field, messages] of Object.entries(body.errors)) {
        if (Array.isArray(messages)) {
            normalized[field] = messages.filter((message): message is string => typeof message === 'string');
        }
    }

    return normalized;
}

// --- Product base URL -----------------------------------------------------------

/**
 * Resolves a product's own base URL (ex: `https://talk.hyvor.com`) from the
 * configured cloud instance (ex: `https://hyvor.com`), by prepending the
 * product's subdomain to the cloud instance's host.
 *
 * @internal
 */
export function resolveProductBaseUrl(cloudInstance: string, product: string): string {
    const url = new URL(cloudInstance);
    return `${url.protocol}//${product}.${url.host}`;
}

// --- Transport -----------------------------------------------------------

export interface TransportConfig {
    httpClient: HttpClient;
    logger: Logger;
    tokenProvider: TokenProvider | null;
    baseUrl: string;
    defaultRetryMaxAttempts: number;
    defaultRetryBackoffFactor: number;
    userAgent: string;
}

/**
 * Executes API requests: builds the HTTP request, authenticates it, retries
 * on transient failures with exponential backoff, and maps error responses
 * to typed exceptions.
 *
 * @internal
 */
export class Transport {

    constructor(private readonly config: TransportConfig) {
    }

    /**
     * Casts a decoded JSON value to `T`. Unlike the PHP SDK (which
     * reflection-denormalizes into real DTO classes), TS response DTOs are
     * plain interfaces with no runtime representation, and the API's JSON
     * field names already match a DTO's property names one-to-one - so
     * there's no transformation to perform, only a compile-time assertion
     * that the caller (a generated resource method) knows the endpoint's
     * true response shape.
     */
    denormalize<T>(data: unknown): T {
        return data as T;
    }

    denormalizeList<T>(data: unknown): T[] {
        return (data ?? []) as T[];
    }

    /**
     * @throws HyvorApiException
     */
    async request(
        method: string,
        path: string,
        jsonBody: unknown = null,
        options?: RequestOptions,
        apiKeyOverride?: string | null,
        extraHeaders: Record<string, string> = {},
    ): Promise<unknown> {
        return this.execute(options, () => this.buildRequest(method, path, jsonBody, apiKeyOverride, options, extraHeaders));
    }

    /**
     * @throws HyvorApiException
     */
    private async execute(options: RequestOptions | undefined, buildRequest: () => Promise<HttpRequest>): Promise<unknown> {
        const maxAttempts = Math.max(1, options?.retryMaxAttempts ?? this.config.defaultRetryMaxAttempts);
        const backoffFactor = options?.retryBackoffFactor ?? this.config.defaultRetryBackoffFactor;

        let lastException: HyvorApiException | null = null;

        for (let attempt = 1; attempt <= maxAttempts; attempt++) {
            const request = await buildRequest();
            let response: HttpResponse;

            try {
                response = await this.config.httpClient.send(request);
            } catch (e) {
                lastException = new NetworkException(e instanceof Error ? e.message : String(e), { cause: e });

                if (attempt < maxAttempts) {
                    await this.waitBeforeRetry(attempt, backoffFactor, null, lastException.message);
                    continue;
                }

                throw lastException;
            }

            if (response.status >= 200 && response.status < 300) {
                return decodeSuccessBody(response.body);
            }

            const exception = mapErrorResponse(response, request.method, request.url);

            if (isRetryable(response.status) && attempt < maxAttempts) {
                lastException = exception;
                await this.waitBeforeRetry(attempt, backoffFactor, response, exception.message);
                continue;
            }

            throw exception;
        }

        throw lastException ?? new NetworkException('Request failed after retries.');
    }

    private async buildRequest(
        method: string,
        path: string,
        jsonBody: unknown,
        apiKeyOverride: string | null | undefined,
        options: RequestOptions | undefined,
        extraHeaders: Record<string, string>,
    ): Promise<HttpRequest> {
        const headers: Record<string, string> = {
            'Authorization': `Bearer ${await this.resolveToken(apiKeyOverride)}`,
            'Accept': 'application/json',
            'User-Agent': this.config.userAgent,
        };

        // extraHeaders carries structural headers (client-level default
        // headers from Talk's website(), and endpoint-specific ones like
        // X-Newsletter-Id); options.headers is the caller's per-call
        // override and is applied last so it always wins.
        for (const [name, value] of Object.entries({ ...extraHeaders, ...(options?.headers ?? {}) })) {
            headers[name] = value;
        }

        let body: string | undefined;
        if (jsonBody !== null) {
            headers['Content-Type'] = 'application/json';
            body = JSON.stringify(jsonBody);
        }

        return { method, url: this.config.baseUrl + path, headers, body };
    }

    /**
     * @throws AuthenticationException
     */
    private async resolveToken(apiKeyOverride: string | null | undefined): Promise<string> {
        if (apiKeyOverride) {
            return apiKeyOverride;
        }

        if (this.config.tokenProvider) {
            return this.config.tokenProvider.getToken();
        }

        throw new AuthenticationException(
            'No credentials configured. Provide a cloudApiKey/tokenProvider to the client, '
            + 'or pass a resource-level API key when accessing the resource (ex: talk.website(id, apiKey)).',
        );
    }

    private async waitBeforeRetry(attempt: number, backoffFactor: number, response: HttpResponse | null, reason: string): Promise<void> {
        const delayMs = this.computeBackoffDelayMs(attempt, backoffFactor, response);

        this.config.logger.warning('Hyvor SDK: retrying request', { attempt, delayMs, reason });

        await sleep(delayMs);
    }

    private computeBackoffDelayMs(attempt: number, backoffFactor: number, response: HttpResponse | null): number {
        const retryAfter = response?.headers['retry-after'];
        if (retryAfter !== undefined && retryAfter !== '' && !isNaN(Number(retryAfter))) {
            return Number(retryAfter) * 1000;
        }

        return Math.round(BASE_RETRY_DELAY_MS * backoffFactor ** (attempt - 1));
    }

}

function decodeSuccessBody(raw: string): unknown {
    return raw === '' ? null : JSON.parse(raw);
}

function sleep(ms: number): Promise<void> {
    return new Promise(resolve => setTimeout(resolve, ms));
}

// --- Transport builder -----------------------------------------------------------

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
