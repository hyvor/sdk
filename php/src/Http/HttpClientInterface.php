<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Http;

/**
 * The HTTP client interface used by the SDK to send requests.
 *
 * Implement this to plug in your own HTTP stack (Guzzle, Symfony HttpClient,
 * a mock for tests, etc). A cURL-based default implementation is used when
 * none is provided to HyvorClient.
 */
interface HttpClientInterface
{
    /**
     * @throws HttpTransportException if the request could not be sent
     *         (connection failure, timeout, DNS error, etc). Do not throw
     *         for HTTP-level error status codes (4xx/5xx) — return the
     *         response as-is and let the SDK map it to the right exception.
     */
    public function sendRequest(HttpRequest $request): HttpResponse;
}
