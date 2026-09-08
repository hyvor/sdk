import { FakeHttpClient, jsonResponse } from '@hyvor/sdk-core';
import { describe, expect, it } from 'vitest';
import { client, PROJECT_ID } from './Support/RelayTestCase.js';

const sampleSend = (overrides: Record<string, unknown> = {}) => ({
    id: 1,
    uuid: '00000000-0000-4000-8000-000000000000',
    created_at: 1700000000,
    from_address: 'from@example.com',
    from_name: null,
    subject: 'Hi',
    size_bytes: 1024,
    queued: true,
    send_after: 1700000000,
    recipients: [],
    attempts: [],
    feedback: [],
    ...overrides,
});

describe('Sends', () => {
    it('list returns sends scoped to the project', async () => {
        const http = new FakeHttpClient();
        http.queueResponse(jsonResponse(200, [sampleSend()]));

        const sends = await client(http).project(PROJECT_ID).sends.list();

        expect(sends).toHaveLength(1);
        expect(sends[0].id).toBe(1);

        const request = http.requests[0];
        expect(request.method).toBe('GET');
        expect(request.url).toBe('https://relay.hyvor.com/api/console/sends');
        expect(request.headers['X-Project-Id']).toBe(String(PROJECT_ID));
    });

    it('get fetches by id with the id in the path', async () => {
        const http = new FakeHttpClient();
        http.queueResponse(jsonResponse(200, sampleSend({ id: 7 })));

        const send = await client(http).project(PROJECT_ID).sends.get(7);

        expect(send.id).toBe(7);

        const request = http.requests[0];
        expect(request.method).toBe('GET');
        expect(request.url).toBe('https://relay.hyvor.com/api/console/sends/7');
    });

    it('send POSTs the email body', async () => {
        const http = new FakeHttpClient();
        http.queueResponse(jsonResponse(200, { id: 10, message_id: 'msg-id@example.com' }));

        const result = await client(http).project(PROJECT_ID).sends.send({
            from: 'from@example.com',
            to: 'to@example.com',
            subject: 'Hi',
            body_html: '<p>Hello</p>',
        });

        expect(result).toEqual({ id: 10, message_id: 'msg-id@example.com' });

        const request = http.requests[0];
        expect(request.method).toBe('POST');
        expect(request.url).toBe('https://relay.hyvor.com/api/console/sends');
        expect(JSON.parse(request.body)).toEqual({
            from: 'from@example.com',
            to: 'to@example.com',
            subject: 'Hi',
            body_html: '<p>Hello</p>',
        });
    });

    it('retry POSTs to /sends/{id}/retry', async () => {
        const http = new FakeHttpClient();
        http.queueResponse(jsonResponse(200, { retried_recipients: 2, send: sampleSend({ id: 7 }) }));

        const result = await client(http).project(PROJECT_ID).sends.retry(7, { recipient_ids: [1, 2] });

        expect(result).toEqual({ retried_recipients: 2, send: sampleSend({ id: 7 }) });

        const request = http.requests[0];
        expect(request.method).toBe('POST');
        expect(request.url).toBe('https://relay.hyvor.com/api/console/sends/7/retry');
        expect(JSON.parse(request.body)).toEqual({ recipient_ids: [1, 2] });
    });

    it('getbyuuid fetches by uuid', async () => {
        const uuid = '11111111-2222-3333-8444-555555555555';
        const http = new FakeHttpClient();
        http.queueResponse(jsonResponse(200, sampleSend({ id: 7, uuid })));

        const send = await client(http).project(PROJECT_ID).sends.getbyuuid(uuid);

        expect(send.uuid).toBe(uuid);

        const request = http.requests[0];
        expect(request.method).toBe('GET');
        expect(request.url).toBe(`https://relay.hyvor.com/api/console/sends/uuid/${uuid}`);
    });
});
