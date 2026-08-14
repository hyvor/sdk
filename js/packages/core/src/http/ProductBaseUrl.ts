/**
 * Resolves a product's own base URL (ex: `https://talk.hyvor.com`) from the
 * configured cloud instance (ex: `https://hyvor.com`), by prepending the
 * product's subdomain to the cloud instance's host.
 *
 * @internal
 */
export function resolveProductBaseUrl(cloudInstance: string, product: string): string {
    const url = new URL(cloudInstance);
    return `${url.protocol}//${product}.${url.host}`;
}
