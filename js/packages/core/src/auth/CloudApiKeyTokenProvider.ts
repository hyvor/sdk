import { AuthenticationException } from '../exceptions/AuthenticationException.js';
import { HttpClient } from '../http/HttpClient.js';
import { Logger } from '../logging/Logger.js';
import { VERSION } from '../version.js';
import { TokenProvider } from './TokenProvider.js';

const TOKEN_EXCHANGE_PATH = '/api/cloud/token';
const EXPIRY_LEEWAY_SECONDS = 30;

interface TokenExchangeResponse {
    token?: unknown;
    expires_in?: unknown;
}

/**
 * Exchanges a Cloud API key for a short-lived JWT, caching it until shortly
 * before it expires.
 */
export class CloudApiKeyTokenProvider implements TokenProvider {

    private cachedToken: string | null = null;
    private cachedTokenExpiresAtSeconds: number | null = null;

    constructor(
        private readonly cloudApiKey: string,
        private readonly baseUrl: string,
        private readonly httpClient: HttpClient,
        private readonly logger: Logger,
    ) {
    }

    async getToken(): Promise<string> {
        const nowSeconds = Date.now() / 1000;

        if (
            this.cachedToken !== null
            && this.cachedTokenExpiresAtSeconds !== null
            && this.cachedTokenExpiresAtSeconds > nowSeconds + EXPIRY_LEEWAY_SECONDS
        ) {
            return this.cachedToken;
        }

        return this.exchangeToken();
    }

    private async exchangeToken(): Promise<string> {
        this.logger.debug('Hyvor SDK: exchanging cloud API key for a JWT token.');

        let response;
        try {
            response = await this.httpClient.send({
                method: 'POST',
                url: this.baseUrl + TOKEN_EXCHANGE_PATH,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'User-Agent': `hyvor/sdk-js/${VERSION}`,
                },
                body: JSON.stringify({ api_key: this.cloudApiKey }),
            });
        } catch (e) {
            throw new AuthenticationException(
                `Failed to exchange the cloud API key for a JWT token: ${e instanceof Error ? e.message : String(e)}`,
                null,
                null,
                { cause: e },
            );
        }

        if (response.status < 200 || response.status >= 300) {
            throw new AuthenticationException(
                `Failed to exchange the cloud API key for a JWT token (HTTP ${response.status}).`,
                response.status,
            );
        }

        const data: TokenExchangeResponse = response.body === '' ? {} : JSON.parse(response.body);
        const token = data.token;
        const expiresIn = data.expires_in;

        if (typeof token !== 'string' || token === '') {
            throw new AuthenticationException('Token exchange response did not include a token.');
        }

        this.cachedToken = token;
        this.cachedTokenExpiresAtSeconds = Date.now() / 1000 + (typeof expiresIn === 'number' ? expiresIn : 3600);

        return token;
    }
}
