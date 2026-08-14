import { HyvorApiException } from './HyvorApiException.js';

/**
 * Thrown when the API rejects a request due to invalid input (HTTP 422).
 */
export class ValidationFailedException extends HyvorApiException {
    /**
     * @param errors Field name => list of error messages.
     */
    constructor(
        message: string,
        public readonly errors: Record<string, string[]> = {},
        responseBody: unknown = null,
    ) {
        super(message, 422, responseBody);
    }
}
