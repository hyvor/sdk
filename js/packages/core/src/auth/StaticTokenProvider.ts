import { TokenProvider } from './TokenProvider.js';

/**
 * Returns a fixed, pre-obtained token (ex: a JWT issued by an internal
 * integration) on every call.
 */
export class StaticTokenProvider implements TokenProvider {
    constructor(private readonly token: string) {
    }

    async getToken(): Promise<string> {
        return this.token;
    }
}
