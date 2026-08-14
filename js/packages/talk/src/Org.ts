import { WebsitesResource } from './Org/WebsitesResource.js';
import type { Transport } from '@hyvor/sdk-core';

/**
 * Org-level access to resources, accessible via `client.org`.
 *
 * Requires org-level auth (a cloud API key or token provider), since it
 * is not scoped to a single resource.
 */
export class Org {
    readonly websites: WebsitesResource;

    constructor(transport: Transport) {
        this.websites = new WebsitesResource(transport);
    }
}
