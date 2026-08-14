import type { Newsletter as Newsletter2 } from './Dto/Newsletter.js';
import type { UpdateNewsletterInput } from './Dto/UpdateNewsletterInput.js';
import { ApiKeyResource } from './Newsletter/ApiKeyResource.js';
import { ExportResource } from './Newsletter/ExportResource.js';
import { ImportResource } from './Newsletter/ImportResource.js';
import { IssueResource } from './Newsletter/IssueResource.js';
import { ListResource } from './Newsletter/ListResource.js';
import { MediaResource } from './Newsletter/MediaResource.js';
import { SendingProfileResource } from './Newsletter/SendingProfileResource.js';
import { SubscriberMetadataResource } from './Newsletter/SubscriberMetadataResource.js';
import { SubscriberResource } from './Newsletter/SubscriberResource.js';
import { TemplateResource } from './Newsletter/TemplateResource.js';
import { UserResource } from './Newsletter/UserResource.js';
import type { RequestOptions, Transport } from '@hyvor/sdk-core';

/**
 * Resource-level access to a single newsletter, accessible via
 * `client.newsletter(newsletterId)`.
 *
 * Authenticated either with the client's org-level auth (a cloud API
 * key or token provider, which must have access to this newsletter), or
 * with a resource-level API key, passed as `apiKey`.
 */
export class Newsletter {
    readonly apiKeyResource: ApiKeyResource;
    readonly export: ExportResource;
    readonly import: ImportResource;
    readonly issue: IssueResource;
    readonly list: ListResource;
    readonly media: MediaResource;
    readonly sendingProfile: SendingProfileResource;
    readonly subscriber: SubscriberResource;
    readonly subscriberMetadata: SubscriberMetadataResource;
    readonly template: TemplateResource;
    readonly user: UserResource;
    private readonly resourceHeaders: Record<string, string>;

    constructor(
        readonly transport: Transport,
        private readonly newsletterId: number | string,
        private readonly apiKey: string | null = null,
        private readonly headers: Record<string, string> = {},
    ) {
        this.resourceHeaders = { 'X-Newsletter-Id': String(newsletterId), ...headers };

        this.apiKeyResource = new ApiKeyResource(this);
        this.export = new ExportResource(this);
        this.import = new ImportResource(this);
        this.issue = new IssueResource(this);
        this.list = new ListResource(this);
        this.media = new MediaResource(this);
        this.sendingProfile = new SendingProfileResource(this);
        this.subscriber = new SubscriberResource(this);
        this.subscriberMetadata = new SubscriberMetadataResource(this);
        this.template = new TemplateResource(this);
        this.user = new UserResource(this);
    }

    path(suffix: string = ''): string {
        return suffix;
    }

    async request(method: string, path: string, jsonBody: unknown = null, options?: RequestOptions): Promise<unknown> {
        return this.transport.request(method, path, jsonBody, options, this.apiKey, this.resourceHeaders);
    }

    /**
     * GET /api/console/newsletter
     */
    async get(options?: RequestOptions): Promise<Newsletter2> {
        const result = await this.request('GET', this.path('/api/console/newsletter'), null, options);

        return this.transport.denormalize<Newsletter2>(result);
    }

    /**
     * DELETE /api/console/newsletter
     */
    async delete(options?: RequestOptions): Promise<void> {
        await this.request('DELETE', this.path('/api/console/newsletter'), null, options);
    }

    /**
     * PATCH /api/console/newsletter
     */
    async update(data: UpdateNewsletterInput, options?: RequestOptions): Promise<Newsletter2> {
        const result = await this.request('PATCH', this.path('/api/console/newsletter'), data, options);

        return this.transport.denormalize<Newsletter2>(result);
    }
}
