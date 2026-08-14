import { FakeHttpClient, jsonResponse, StaticTokenProvider, ValidationFailedException } from '@hyvor/sdk-core';
import { describe, expect, it } from 'vitest';
import { TalkClient } from '../src/TalkClient.js';
import { baseUrl, client, WEBSITE_ID } from './Support/TalkTestCase.js';

describe('Website', () => {
    it('create sends name and domain', async () => {
        const http = new FakeHttpClient();
        http.queueResponse(jsonResponse(201, {}));

        await client(http).org.websites.create({
            name: 'My Blog',
            domain: 'blog.example.com',
        });

        const request = http.requests[0];
        expect(request.method).toBe('POST');
        expect(request.url).toBe('https://talk.hyvor.com/api/console/v1/websites');
        expect(JSON.parse(request.body ?? '{}')).toEqual({ name: 'My Blog', domain: 'blog.example.com' });
        expect(request.headers['Authorization']).toBe('Bearer test-jwt-token');
    });

    it('create throws ValidationFailedException on a 422', async () => {
        const http = new FakeHttpClient();
        http.queueResponse(jsonResponse(422, {
            message: 'The given data was invalid.',
            errors: { domain: ['The domain has already been taken.'] },
        }));

        await expect(client(http).org.websites.create({ name: 'X', domain: 'taken.com' }))
            .rejects.toSatisfy((e: unknown) => {
                expect(e).toBeInstanceOf(ValidationFailedException);
                expect((e as ValidationFailedException).statusCode).toBe(422);
                expect((e as ValidationFailedException).errors.domain).toEqual(['The domain has already been taken.']);
                return true;
            });
    });

    it('delete sends a DELETE request', async () => {
        const http = new FakeHttpClient();
        http.queueResponse(jsonResponse(200, {}));

        await client(http).website(WEBSITE_ID).delete();

        const request = http.requests[0];
        expect(request.method).toBe('DELETE');
        expect(request.url).toBe(`${baseUrl()}/website`);
        expect(request.headers['Authorization']).toBe('Bearer test-jwt-token');
    });

    it('a resource-level API key overrides the client auth', async () => {
        const http = new FakeHttpClient();
        http.queueResponse(jsonResponse(200, {}));

        await client(http).website(WEBSITE_ID, 'resource-api-key').delete();

        expect(http.requests[0].headers['Authorization']).toBe('Bearer resource-api-key');
    });

    it('productUrl overrides the cloudInstance-derived URL', async () => {
        const http = new FakeHttpClient();
        http.queueResponse(jsonResponse(200, {}));

        const talk = new TalkClient({
            httpClient: http,
            tokenProvider: new StaticTokenProvider('test-jwt-token'),
            productUrl: 'https://talk.example.com',
        });

        await talk.website(WEBSITE_ID).delete();

        expect(http.requests[0].url).toBe(`https://talk.example.com/api/console/v1/${WEBSITE_ID}/website`);
    });
});
