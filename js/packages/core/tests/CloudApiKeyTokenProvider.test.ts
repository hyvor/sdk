import { describe, expect, it } from 'vitest';
import { CloudApiKeyTokenProvider } from '../src/auth.js';
import { AuthenticationException } from '../src/exceptions.js';
import { NullLogger } from '../src/logging.js';
import { FakeHttpClient, jsonResponse } from '../src/testing.js';

describe('CloudApiKeyTokenProvider', () => {
    it('exchanges the cloud API key for a token, then caches it', async () => {
        const http = new FakeHttpClient();
        http.queueResponse(jsonResponse(200, { token: 'short-lived-jwt', expires_in: 3600 }));

        const provider = new CloudApiKeyTokenProvider('cloud-key', 'https://hyvor.com', http, new NullLogger());

        await expect(provider.getToken()).resolves.toBe('short-lived-jwt');
        await expect(provider.getToken()).resolves.toBe('short-lived-jwt');

        expect(http.requests).toHaveLength(1);
        expect(http.requests[0].url).toBe('https://hyvor.com/api/cloud/token');
    });

    it('throws AuthenticationException when the exchange fails', async () => {
        const http = new FakeHttpClient();
        http.queueResponse(jsonResponse(401, { message: 'Invalid API key' }));

        const provider = new CloudApiKeyTokenProvider('bad-key', 'https://hyvor.com', http, new NullLogger());

        await expect(provider.getToken()).rejects.toBeInstanceOf(AuthenticationException);
    });
});
