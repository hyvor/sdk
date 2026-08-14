import { FakeHttpClient, StaticTokenProvider } from '@hyvor/sdk-core';
import { TalkClient } from '../../src/TalkClient.js';

/**
 * Shared helpers for Talk resource tests: a preconfigured client, so each
 * test file doesn't have to redefine the auth/http wiring.
 */
export const WEBSITE_ID = 42;

export function client(httpClient: FakeHttpClient, retryMaxAttempts = 3): TalkClient {
    return new TalkClient({
        httpClient,
        tokenProvider: new StaticTokenProvider('test-jwt-token'),
        retryMaxAttempts,
    });
}

export function baseUrl(): string {
    return `https://talk.hyvor.com/api/console/v1/${WEBSITE_ID}`;
}
