import { describe, expect, it } from 'vitest';
import { StaticTokenProvider } from '../../src/auth/StaticTokenProvider.js';

describe('StaticTokenProvider', () => {
    it('returns the given token every time', async () => {
        const provider = new StaticTokenProvider('static-jwt');

        await expect(provider.getToken()).resolves.toBe('static-jwt');
        await expect(provider.getToken()).resolves.toBe('static-jwt');
    });
});
