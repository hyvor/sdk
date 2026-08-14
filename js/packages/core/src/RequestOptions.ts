/**
 * Per-request overrides for retries and headers. Any field left unset falls
 * back to the client's default configuration.
 *
 * `headers` are merged into the request, on top of any default headers set
 * via a resource client factory (ex: `talk.website(id, apiKey, headers)`).
 * Useful, for example, to authenticate as a specific moderator instead of
 * the website owner by setting `X-AUTH-USER-EMAIL` or `X-AUTH-USER-SSO-ID`.
 */
export interface RequestOptions {
    retryMaxAttempts?: number;
    retryBackoffFactor?: number;
    headers?: Record<string, string>;
}
