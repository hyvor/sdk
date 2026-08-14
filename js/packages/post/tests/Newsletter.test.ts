import { FakeHttpClient, jsonResponse } from '@hyvor/sdk-core';
import { describe, expect, it } from 'vitest';
import { client, NEWSLETTER_ID } from './Support/PostTestCase.js';

describe('Newsletter', () => {
    it('get sends the X-Newsletter-Id header instead of embedding the ID in the path', async () => {
        const http = new FakeHttpClient();
        http.queueResponse(jsonResponse(200, {
            id: NEWSLETTER_ID,
            name: 'My Newsletter',
            subdomain: 'my-newsletter',
            created_at: 1700000000,
            is_rtl: false,
            metadata: {},
        }));

        const newsletter = await client(http).newsletter(NEWSLETTER_ID).get();

        expect(newsletter.name).toBe('My Newsletter');

        const request = http.requests[0];
        expect(request.method).toBe('GET');
        expect(request.url).toBe('https://post.hyvor.com/api/console/newsletter');
        expect(request.headers['X-Newsletter-Id']).toBe(String(NEWSLETTER_ID));
        expect(request.headers['Authorization']).toBe('Bearer test-jwt-token');
    });

    it('a sub-resource whose group name collides with the fixed apiKey param is exposed as apiKeyResource', async () => {
        const http = new FakeHttpClient();
        http.queueResponse(jsonResponse(200, []));

        await client(http).newsletter(NEWSLETTER_ID).apiKeyResource.list();

        expect(http.requests[0].headers['X-Newsletter-Id']).toBe(String(NEWSLETTER_ID));
    });

    it('a verbatim method:{name} tag (ex: "gettestdata") is kept as-is, matching the PHP SDK', async () => {
        const http = new FakeHttpClient();
        http.queueResponse(jsonResponse(200, { progress: 42 }));

        const result = await client(http).newsletter(NEWSLETTER_ID).issue.gettestdata(1);

        expect(result).toEqual({ progress: 42 });
        expect(http.requests[0].url).toBe('https://post.hyvor.com/api/console/issues/1/test');
    });
});
