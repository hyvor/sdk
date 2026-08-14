import { HttpClient, HttpRequest, HttpResponse } from './http.js';

export class FakeHttpTransportError extends Error {
}

/**
 * A queue-driven {@link HttpClient} test double: queue up responses (or
 * errors) in the order requests are expected to be sent, then assert against
 * the requests that were actually made.
 */
export class FakeHttpClient implements HttpClient {

    private queue: Array<HttpResponse | Error> = [];
    readonly requests: HttpRequest[] = [];

    queueResponse(response: HttpResponse): void {
        this.queue.push(response);
    }

    queueError(error: Error): void {
        this.queue.push(error);
    }

    async send(request: HttpRequest): Promise<HttpResponse> {
        this.requests.push(request);

        const next = this.queue.shift();

        if (next === undefined) {
            throw new FakeHttpTransportError('FakeHttpClient: no queued response left.');
        }

        if (next instanceof Error) {
            throw next;
        }

        return next;
    }

}

/**
 * Builds a JSON {@link HttpResponse} - a convenience for tests, mirroring
 * how the real API responds to a successful/erroring request.
 */
export function jsonResponse(status: number, data: unknown, headers: Record<string, string> = {}): HttpResponse {
    return {
        status,
        headers: { 'content-type': 'application/json', ...headers },
        body: JSON.stringify(data),
    };
}
