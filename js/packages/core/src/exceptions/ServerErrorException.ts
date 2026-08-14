import { HyvorApiException } from './HyvorApiException.js';

/**
 * Thrown when the API responds with a server error (HTTP 5xx).
 */
export class ServerErrorException extends HyvorApiException {
}
