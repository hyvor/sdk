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

/**
 * Generic fallback for API error responses that don't map to a more
 * specific exception type.
 */
export class ApiException extends HyvorApiException {
}

/**
 * Thrown when the API rejects the request's credentials (HTTP 401/403), or
 * when no credentials are configured at all.
 */
export class AuthenticationException extends HyvorApiException {
}

/**
 * Thrown when the requested resource does not exist (HTTP 404).
 */
export class NotFoundException extends HyvorApiException {
}

/**
 * Thrown when the API responds with a server error (HTTP 5xx).
 */
export class ServerErrorException extends HyvorApiException {
}

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
