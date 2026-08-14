/**
 * Base class for all errors returned by the Hyvor API.
 */
export abstract class HyvorApiException extends Error {
    constructor(
        message: string,
        public readonly statusCode: number | null = null,
        public readonly responseBody: unknown = null,
        options?: { cause?: unknown },
    ) {
        super(message, options);
        this.name = this.constructor.name;
    }
}
