import { TokenProvider } from '../auth/TokenProvider.js';
import { AuthenticationException, HyvorApiException, NetworkException } from '../exceptions.js';
import { Logger } from '../logging/Logger.js';
import { RequestOptions } from '../RequestOptions.js';
import { isRetryable, mapErrorResponse } from './ErrorMapper.js';
import { HttpClient, HttpRequest, HttpResponse } from './HttpClient.js';

const BASE_RETRY_DELAY_MS = 200;

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
