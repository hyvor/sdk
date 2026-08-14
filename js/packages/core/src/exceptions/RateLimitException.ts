import { HyvorApiException } from './HyvorApiException.js';

/**
 * Thrown when the API rate limit has been exceeded (HTTP 429).
 */
export class RateLimitException extends HyvorApiException {
    constructor(
        message: string,
        public readonly retryAfterSeconds: number | null = null,
        responseBody: unknown = null,
    ) {
        super(message, 429, responseBody);
    }
}
