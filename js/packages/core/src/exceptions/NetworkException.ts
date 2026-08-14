import { HyvorApiException } from './HyvorApiException.js';

/**
 * Thrown when a request could not be sent at all (connection failure,
 * timeout, DNS error, etc), as opposed to the server returning an error
 * status.
 */
export class NetworkException extends HyvorApiException {
    constructor(message: string, options?: { cause?: unknown }) {
        super(message, null, null, options);
    }
}
