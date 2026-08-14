import { FakeHttpClient, StaticTokenProvider } from '@hyvor/sdk-core';
import { PostClient } from '../../src/PostClient.js';

/**
 * Shared helpers for Post resource tests: a preconfigured client, so each
 * test file doesn't have to redefine the auth/http wiring.
 */
export const NEWSLETTER_ID = 7;

export function client(httpClient: FakeHttpClient, retryMaxAttempts = 3): PostClient {
    return new PostClient({
        httpClient,
        tokenProvider: new StaticTokenProvider('test-jwt-token'),
        retryMaxAttempts,
    });
}
