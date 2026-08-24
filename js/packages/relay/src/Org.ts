import { ProjectsResource } from './Org/ProjectsResource.js';
import type { Transport } from '@hyvor/sdk-core';

/**
 * Org-level access to resources, accessible via `client.org`.
 *
 * Requires org-level auth (a cloud API key or token provider), since it
 * is not scoped to a single resource.
 */
export class Org {
    readonly projects: ProjectsResource;

    constructor(transport: Transport) {
        this.projects = new ProjectsResource(transport);
    }
}
