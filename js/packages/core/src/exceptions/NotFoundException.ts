import { HyvorApiException } from './HyvorApiException.js';

/**
 * Thrown when the requested resource does not exist (HTTP 404).
 */
export class NotFoundException extends HyvorApiException {
}
