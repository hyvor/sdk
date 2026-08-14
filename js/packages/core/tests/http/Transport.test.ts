import { describe, expect, it } from 'vitest';
import { StaticTokenProvider } from '../../src/auth/StaticTokenProvider.js';
import { NetworkException, RateLimitException, ValidationFailedException } from '../../src/exceptions.js';
import { Transport } from '../../src/http/Transport.js';
import { NullLogger } from '../../src/logging/Logger.js';
import { FakeHttpClient, jsonResponse } from '../../src/testing/FakeHttpClient.js';

function makeTransport(http: FakeHttpClient, overrides: Partial<{ retryMaxAttempts: number, retryBackoffFactor: number }> = {}) {
    return new Transport({
        httpClient: http,
        logger: new NullLogger(),
        tokenProvider: new StaticTokenProvider('test-jwt'),
        baseUrl: 'https://talk.hyvor.com',
        defaultRetryMaxAttempts: overrides.retryMaxAttempts ?? 3,
        defaultRetryBackoffFactor: overrides.retryBackoffFactor ?? 0.001, // keep tests fast
        userAgent: 'hyvor/sdk-js-talk/test',
    });
}

describe('Transport', () => {
    it('sends an authenticated request and decodes the JSON body', async () => {
        const http = new FakeHttpClient();
        http.queueResponse(jsonResponse(200, { id: 1, name: 'My Blog' }));

        const transport = makeTransport(http);
        const result = await transport.request('GET', '/api/console/v1/1/website');

        expect(result).toEqual({ id: 1, name: 'My Blog' });
        expect(http.requests[0].headers['Authorization']).toBe('Bearer test-jwt');
        expect(http.requests[0].url).toBe('https://talk.hyvor.com/api/console/v1/1/website');
    });

    it('throws ValidationFailedException on 422 without retrying', async () => {
        const http = new FakeHttpClient();
        http.queueResponse(jsonResponse(422, {
            message: 'The given data was invalid.',
            errors: { domain: ['The domain has already been taken.'] },
        }));

        const transport = makeTransport(http);

        await expect(transport.request('POST', '/api/console/v1/websites', { name: 'X' }))
            .rejects.toSatisfy((e: unknown) => {
                expect(e).toBeInstanceOf(ValidationFailedException);
                expect((e as ValidationFailedException).errors.domain).toEqual(['The domain has already been taken.']);
                return true;
            });

        expect(http.requests).toHaveLength(1);
    });

    it('retries a 429 with backoff before eventually succeeding', async () => {
        const http = new FakeHttpClient();
        http.queueResponse(jsonResponse(429, { message: 'Too many requests' }));
        http.queueResponse(jsonResponse(200, { ok: true }));

        const transport = makeTransport(http, { retryMaxAttempts: 3 });
        const result = await transport.request('GET', '/api/console/v1/1/website');

        expect(result).toEqual({ ok: true });
        expect(http.requests).toHaveLength(2);
    });

    it('gives up after retryMaxAttempts and throws the last error', async () => {
        const http = new FakeHttpClient();
        http.queueResponse(jsonResponse(500, { message: 'Server error' }));
        http.queueResponse(jsonResponse(500, { message: 'Server error' }));

        const transport = makeTransport(http, { retryMaxAttempts: 2 });

        await expect(transport.request('GET', '/api/console/v1/1/website')).rejects.toBeInstanceOf(Error);
        expect(http.requests).toHaveLength(2);
    });

    it('maps a rejected send() to NetworkException and retries', async () => {
        const http = new FakeHttpClient();
        http.queueError(new Error('connection refused'));
        http.queueResponse(jsonResponse(200, { ok: true }));

        const transport = makeTransport(http, { retryMaxAttempts: 2 });
        const result = await transport.request('GET', '/api/console/v1/1/website');

        expect(result).toEqual({ ok: true });
    });

    it('surfaces RateLimitException with retryAfterSeconds when retries are exhausted', async () => {
        const http = new FakeHttpClient();
        http.queueResponse(jsonResponse(429, { message: 'Too many requests' }, { 'retry-after': '5' }));

        const transport = makeTransport(http, { retryMaxAttempts: 1 });

        await expect(transport.request('GET', '/api/console/v1/1/website'))
            .rejects.toSatisfy((e: unknown) => {
                expect(e).toBeInstanceOf(RateLimitException);
                expect((e as RateLimitException).retryAfterSeconds).toBe(5);
                return true;
            });
    });

    it('throws NetworkException after retries are exhausted on repeated send() failures', async () => {
        const http = new FakeHttpClient();
        http.queueError(new Error('timeout'));
        http.queueError(new Error('timeout'));

        const transport = makeTransport(http, { retryMaxAttempts: 2 });

        await expect(transport.request('GET', '/api/console/v1/1/website')).rejects.toBeInstanceOf(NetworkException);
    });
});
