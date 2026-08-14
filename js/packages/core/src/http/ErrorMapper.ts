import { ApiException } from '../exceptions/ApiException.js';
import { AuthenticationException } from '../exceptions/AuthenticationException.js';
import { HyvorApiException } from '../exceptions/HyvorApiException.js';
import { NotFoundException } from '../exceptions/NotFoundException.js';
import { RateLimitException } from '../exceptions/RateLimitException.js';
import { ServerErrorException } from '../exceptions/ServerErrorException.js';
import { ValidationFailedException } from '../exceptions/ValidationFailedException.js';
import { HttpResponse } from './HttpClient.js';

/**
 * @internal
 */
export function mapErrorResponse(response: HttpResponse, requestMethod: string, requestUrl: string): HyvorApiException {
    const statusCode = response.status;
    const body = decodeBody(response.body);

    const message = isRecord(body) && typeof body.message === 'string'
        ? body.message
        : `${requestMethod} ${requestUrl} returned HTTP ${statusCode}`;

    if (statusCode === 422) {
        return new ValidationFailedException(message, normalizeValidationErrors(body), body);
    }
    if (statusCode === 429) {
        return new RateLimitException(message, retryAfterSeconds(response), body);
    }
    if (statusCode === 401 || statusCode === 403) {
        return new AuthenticationException(message, statusCode, body);
    }
    if (statusCode === 404) {
        return new NotFoundException(message, 404, body);
    }
    if (statusCode >= 500) {
        return new ServerErrorException(message, statusCode, body);
    }

    return new ApiException(message, statusCode, body);
}

/**
 * @internal
 */
export function isRetryable(statusCode: number): boolean {
    return statusCode === 429 || statusCode >= 500;
}

function isRecord(value: unknown): value is Record<string, unknown> {
    return typeof value === 'object' && value !== null && !Array.isArray(value);
}

function decodeBody(raw: string): unknown {
    if (raw === '') {
        return null;
    }

    try {
        return JSON.parse(raw);
    } catch {
        return null;
    }
}

function retryAfterSeconds(response: HttpResponse): number | null {
    const header = response.headers['retry-after'];
    return header !== undefined && header !== '' && !isNaN(Number(header)) ? Number(header) : null;
}

function normalizeValidationErrors(body: unknown): Record<string, string[]> {
    if (!isRecord(body) || !isRecord(body.errors)) {
        return {};
    }

    const normalized: Record<string, string[]> = {};

    for (const [field, messages] of Object.entries(body.errors)) {
        if (Array.isArray(messages)) {
            normalized[field] = messages.filter((message): message is string => typeof message === 'string');
        }
    }

    return normalized;
}
