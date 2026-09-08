import { FakeHttpClient, jsonResponse } from '@hyvor/sdk-core';
import { describe, expect, it } from 'vitest';
import { client, PROJECT_ID } from './Support/RelayTestCase.js';

describe('Project', () => {
    it('get sends the X-Project-Id header instead of embedding the ID in the path', async () => {
        const http = new FakeHttpClient();
        http.queueResponse(jsonResponse(200, {
            id: 1,
            created_at: 1700000000,
            name: 'My Project',
            send_type: 'transactional',
        }));

        const project = await client(http).project(PROJECT_ID).get();

        expect(project.name).toBe('My Project');

        const request = http.requests[0];
        expect(request.method).toBe('GET');
        expect(request.url).toBe('https://relay.hyvor.com/api/console/project');
        expect(request.headers['X-Project-Id']).toBe(String(PROJECT_ID));
        expect(request.headers['Authorization']).toBe('Bearer test-jwt-token');
    });

    it('update PATCHes the project name', async () => {
        const http = new FakeHttpClient();
        http.queueResponse(jsonResponse(200, {
            id: 1,
            created_at: 1700000000,
            name: 'Renamed',
            send_type: 'transactional',
        }));

        const project = await client(http).project(PROJECT_ID).update({ name: 'Renamed' });

        expect(project.name).toBe('Renamed');

        const request = http.requests[0];
        expect(request.method).toBe('PATCH');
        expect(request.url).toBe('https://relay.hyvor.com/api/console/project');
        expect(JSON.parse(request.body)).toEqual({ name: 'Renamed' });
    });

    it('org.projects.create POSTs without an X-Project-Id header (org-level)', async () => {
        const http = new FakeHttpClient();
        http.queueResponse(jsonResponse(200, {
            id: 5,
            created_at: 1700000000,
            scopes: ['project:read'],
            user: {
                id: 1,
                name: 'Jane',
                email: 'jane@example.com',
                username: null,
                picture_url: null,
                oidc_sub: null,
            },
            oidc_sub: null,
            project: { id: 5, created_at: 1700000000, name: 'New Project', send_type: 'transactional' },
        }));

        const projectUser = await client(http).org.projects.create({
            name: 'New Project',
            send_type: 'transactional',
        });

        expect(projectUser.project.id).toBe(5);

        const request = http.requests[0];
        expect(request.method).toBe('POST');
        expect(request.url).toBe('https://relay.hyvor.com/api/console/project');
        expect(JSON.parse(request.body)).toEqual({ name: 'New Project', send_type: 'transactional' });
        // org endpoint: no X-Project-Id header
        expect(request.headers['X-Project-Id']).toBeUndefined();
        expect(request.headers['Authorization']).toBe('Bearer test-jwt-token');
    });

    it('sub-resources are scoped to the project via X-Project-Id', async () => {
        const http = new FakeHttpClient();
        http.queueResponse(jsonResponse(200, []));

        await client(http).project(PROJECT_ID).domains.list();

        expect(http.requests[0].headers['X-Project-Id']).toBe(String(PROJECT_ID));
    });
});
