import { FakeHttpClient, StaticTokenProvider } from '@hyvor/sdk-core';
import { RelayClient } from '../../src/RelayClient.js';

/**
 * Shared helpers for Relay resource tests: a preconfigured client, so each
 * test file doesn't have to redefine the auth/http wiring.
 */
export const PROJECT_ID = 42;

export function client(httpClient: FakeHttpClient, retryMaxAttempts = 3): RelayClient {
    return new RelayClient({
        httpClient,
        tokenProvider: new StaticTokenProvider('test-jwt-token'),
        retryMaxAttempts,
    });
}
