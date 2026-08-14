/**
 * A minimal, standard logging interface (a pared-down PSR-3): the SDK only
 * ever logs at `debug` (request/response tracing) and `warning` (retries)
 * level. Implement this to plug in your own logger (`console`, pino, winston,
 * ...).
 */
export interface Logger {
    debug(message: string, context?: Record<string, unknown>): void;
    warning(message: string, context?: Record<string, unknown>): void;
}

/**
 * The default logger: discards everything. Passed when no `logger` option is
 * given.
 */
export class NullLogger implements Logger {
    debug(): void {
    }

    warning(): void {
    }
}
