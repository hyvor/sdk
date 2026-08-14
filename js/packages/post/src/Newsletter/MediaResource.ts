import type { Media } from '../Dto/Media.js';
import type { MediaUploadInput } from '../Dto/MediaUploadInput.js';
import type { Newsletter } from '../Newsletter.js';
import type { RequestOptions } from '@hyvor/sdk-core';

/**
 * `client.newsletter(newsletterId).media`
 */
export class MediaResource {
    constructor(private readonly client: Newsletter) {
    }

    /**
     * POST /api/console/media
     */
    async upload(data: MediaUploadInput, options?: RequestOptions): Promise<Media> {
        const result = await this.client.request('POST', this.client.path('/api/console/media'), data, options);

        return this.client.transport.denormalize<Media>(result);
    }
}
