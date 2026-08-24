import { FakeHttpClient, jsonResponse } from '@hyvor/sdk-core';
import { describe, expect, it } from 'vitest';
import { client, PROJECT_ID } from './Support/RelayTestCase.js';

const sampleDomain = (overrides: Record<string, unknown> = {}) => ({
    id: 1,
    created_at: 1700000000,
    domain: 'example.com',
    status: 'active',
    status_changed_at: 1700000000,
    dkim_selector: 'default',
    dkim_host: 'default._domainkey.example.com',
    dkim_public_key: 'publickey',
    dkim_txt_value: 'v=DKIM1; k=rsa; p=publickey',
    dkim_checked_at: 1700000000,
    dkim_error_message: null,
    ...overrides,
});

describe('Domains', () => {
    it('list returns domains scoped to the project', async () => {
        const http = new FakeHttpClient();
        http.queueResponse(jsonResponse(200, [sampleDomain()]));

        const domains = await client(http).project(PROJECT_ID).domains.list();

        expect(domains).toHaveLength(1);
        expect(domains[0].domain).toBe('example.com');

        const request = http.requests[0];
        expect(request.method).toBe('GET');
        expect(request.url).toBe('https://relay.hyvor.com/api/console/domains');
        expect(request.headers['X-Project-Id']).toBe(String(PROJECT_ID));
    });

    it('create POSTs the domain', async () => {
        const http = new FakeHttpClient();
        http.queueResponse(jsonResponse(200, sampleDomain({ domain: 'mail.example.com' })));

        const domain = await client(http).project(PROJECT_ID).domains.create({ domain: 'mail.example.com' });

        expect(domain.domain).toBe('mail.example.com');

        const request = http.requests[0];
        expect(request.method).toBe('POST');
        expect(request.url).toBe('https://relay.hyvor.com/api/console/domains');
        expect(JSON.parse(request.body)).toEqual({ domain: 'mail.example.com' });
    });

    it('verify POSTs to /domains/verify', async () => {
        const http = new FakeHttpClient();
        http.queueResponse(jsonResponse(200, sampleDomain({ status: 'active' })));

        const domain = await client(http).project(PROJECT_ID).domains.verify({ id: 1 });

        expect(domain.status).toBe('active');

        const request = http.requests[0];
        expect(request.method).toBe('POST');
        expect(request.url).toBe('https://relay.hyvor.com/api/console/domains/verify');
        expect(JSON.parse(request.body)).toEqual({ id: 1 });
    });

    it('get fetches by id or domain via /domains/by', async () => {
        const http = new FakeHttpClient();
        http.queueResponse(jsonResponse(200, sampleDomain()));

        const domain = await client(http).project(PROJECT_ID).domains.get({ domain: 'example.com' });

        expect(domain.domain).toBe('example.com');

        const request = http.requests[0];
        expect(request.method).toBe('GET');
        expect(request.url).toBe('https://relay.hyvor.com/api/console/domains/by');
        expect(JSON.parse(request.body)).toEqual({ domain: 'example.com' });
    });

    it('delete sends the body on DELETE', async () => {
        const http = new FakeHttpClient();
        http.queueResponse(jsonResponse(200, {}));

        await client(http).project(PROJECT_ID).domains.delete({ id: 1 });

        const request = http.requests[0];
        expect(request.method).toBe('DELETE');
        expect(request.url).toBe('https://relay.hyvor.com/api/console/domains');
        expect(JSON.parse(request.body)).toEqual({ id: 1 });
    });
});
