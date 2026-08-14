import { DomainResource } from './Org/DomainResource.js';
import { NewslettersResource } from './Org/NewslettersResource.js';
import type { Transport } from '@hyvor/sdk-core';

/**
 * Org-level access to resources, accessible via `client.org`.
 *
 * Requires org-level auth (a cloud API key or token provider), since it
 * is not scoped to a single resource.
 */
export class Org {
    readonly domain: DomainResource;
    readonly newsletters: NewslettersResource;

    constructor(transport: Transport) {
        this.domain = new DomainResource(transport);
        this.newsletters = new NewslettersResource(transport);
    }
}
