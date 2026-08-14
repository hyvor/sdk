import { HyvorApiException } from './HyvorApiException.js';

/**
 * Generic fallback for API error responses that don't map to a more
 * specific exception type.
 */
export class ApiException extends HyvorApiException {
}
