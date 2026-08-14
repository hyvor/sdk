import { HyvorApiException } from './HyvorApiException.js';

/**
 * Thrown when the API rejects the request's credentials (HTTP 401/403), or
 * when no credentials are configured at all.
 */
export class AuthenticationException extends HyvorApiException {
}
