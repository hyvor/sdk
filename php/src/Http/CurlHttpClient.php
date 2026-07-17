<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Http;

/**
 * Default HttpClientInterface implementation, using ext-curl.
 */
final class CurlHttpClient implements HttpClientInterface
{
    public function sendRequest(HttpRequest $request): HttpResponse
    {
        $handle = curl_init();

        $headerLines = [];
        foreach ($request->headers as $name => $value) {
            $headerLines[] = "$name: $value";
        }

        curl_setopt_array($handle, [
            CURLOPT_URL => $request->url,
            CURLOPT_CUSTOMREQUEST => strtoupper($request->method),
            CURLOPT_HTTPHEADER => $headerLines,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_CONNECTTIMEOUT_MS => $request->connectTimeoutMs,
            CURLOPT_TIMEOUT_MS => $request->requestTimeoutMs,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HEADERFUNCTION => function ($curl, string $line) use (&$responseHeaders) {
                $responseHeaders ??= [];
                $parts = explode(':', $line, 2);
                if (count($parts) === 2) {
                    $responseHeaders[strtolower(trim($parts[0]))] = trim($parts[1]);
                }
                return strlen($line);
            },
        ]);

        if ($request->body !== null) {
            curl_setopt($handle, CURLOPT_POSTFIELDS, $request->body);
        }

        $responseHeaders = [];
        $body = curl_exec($handle);

        if ($body === false) {
            $error = curl_error($handle);
            $errno = curl_errno($handle);
            curl_close($handle);

            throw new HttpTransportException(
                "HTTP request to {$request->url} failed: $error (curl errno $errno)"
            );
        }

        $statusCode = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
        curl_close($handle);

        return new HttpResponse($statusCode, $responseHeaders, (string) $body);
    }
}
