# hyvor/sdk-php

Official PHP SDK for Hyvor products (Talk, Post).

## Install

```bash
composer require hyvor/sdk-php
```

Requires PHP >= 8.1.

## Usage

```php
use Hyvor\Sdk\HyvorClient;
use Hyvor\Sdk\Talk\Dto\CreateWebsiteRequest;

$client = new HyvorClient(
    cloudApiKey: 'your-cloud-api-key', // or jwtToken: '...'
);

// GET /website
$website = $client->talk->website->get();

// POST /website
$website = $client->talk->website->create(
    new CreateWebsiteRequest(name: 'My Blog', domain: 'blog.example.com')
);
```

### Configuration

```php
$client = new HyvorClient(
    cloudApiKey: '...',
    cloudInstance: 'https://hyvor.com', // default
    logger: $psrLogger,                 // PSR-3 logger, default NullLogger
    httpClient: $myHttpClient,          // Hyvor\Sdk\Http\HttpClientInterface, default cURL-based client
    connectTimeoutMs: 5000,
    requestTimeoutMs: 10000,
    retryMaxAttempts: 3,
    retryBackoffFactor: 2.0,
);
```

### Request options

Per-request overrides (timeouts, retries, idempotency keys):

```php
use Hyvor\Sdk\RequestOptions;

$client->talk->website->create(
    new CreateWebsiteRequest(name: 'My Blog', domain: 'blog.example.com'),
    new RequestOptions(idempotencyKey: 'unique-key-per-operation'),
);
```

### Errors

All API errors extend `Hyvor\Sdk\Exceptions\HyvorApiException`:

- `ValidationFailedException` (422) — has `$errors` (field => messages)
- `RateLimitException` (429) — has `$retryAfterSeconds`
- `AuthenticationException` (401/403)
- `NotFoundException` (404)
- `ServerErrorException` (5xx)
- `NetworkException` — request could not be sent (connection/timeout)
- `ApiException` — fallback for other error statuses

Requests to `RateLimitException` and `ServerErrorException`-triggering statuses are retried automatically with exponential backoff before the exception is thrown.

## Development

```bash
composer install
composer test
```

## Notes

The `website` endpoints are not publicly documented. This SDK assumes:

- Talk endpoints are reached through the configured `cloudInstance` at `/api/talk/*` (there is no separate per-product instance URL in the client config).
- A `cloudApiKey` is exchanged for a short-lived JWT via `POST {cloudInstance}/api/cloud/token`.

Adjust `Hyvor\Sdk\Auth\TokenProvider` and the resource paths if the real API differs.
