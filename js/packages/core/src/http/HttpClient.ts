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
 * {@link FakeHttpClient}).
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
