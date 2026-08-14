/**
 * Resolves the bearer token used to authenticate requests to the Hyvor API.
 *
 * Implement this to plug in a custom way of obtaining a token (ex: one
 * issued by an internal integration).
 */
export interface TokenProvider {
    getToken(): Promise<string>;
}
