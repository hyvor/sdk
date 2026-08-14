# hyvor/sdk-{product}-php

Official PHP SDKs for HYVOR Products, published as one Composer package per product so you only
install what you use.

## Install

```bash
composer require hyvor/sdk-talk   # Hyvor Talk
composer require hyvor/sdk-post   # Hyvor Post
```

Both packages depend on `hyvor/sdk-core` (installed automatically) for shared HTTP transport,
auth, and serialization - you never require `hyvor/sdk-core` yourself.

Requires PHP >= 8.4. The SDKs talk HTTP through [PSR-18](https://www.php-fig.org/psr/psr-18/) /
[PSR-17](https://www.php-fig.org/psr/psr-17/) and don't ship their own HTTP client. If your
project already has one installed (Guzzle, Symfony HttpClient, Nyholm, etc.), it's discovered
automatically via [php-http/discovery](https://github.com/php-http/discovery) - no extra wiring
needed.

## Usage

### Hyvor Talk

```php
use Hyvor\Sdk\Talk\TalkClient;
use Hyvor\Sdk\Talk\Dto\Comment\ListCommentsRequest;
use Hyvor\Sdk\Talk\Dto\Website\CreateWebsiteRequest;

// org-level access, via a cloud API key
$talk = new TalkClient(
    cloudApiKey: 'your-cloud-api-key', // or tokenProvider: new SomeTokenProviderInterface()
);

// GET /api/console/v1/{id}/website - resource-level access to a specific website.
// The cloud API key/token provider must have access to this website.
$website = $talk->website($websiteId)->get();

// POST /api/console/v1/websites - org-level endpoint, not scoped to one website
$website = $talk->websites->create(
    new CreateWebsiteRequest(name: 'My Blog', domain: 'blog.example.com')
);

// every Console API resource hangs off the website client, e.g.:
$comments = $talk->website($websiteId)->comments->list(new ListCommentsRequest(limit: 10));
$pages = $talk->website($websiteId)->pages->list();
$moderators = $talk->website($websiteId)->moderators->list();
```

`$talk->website($websiteId)` exposes: `comments`, `reactions`, `ratings`, `pages`, `users`,
`analytics`, `moderators`, `emailDomain`, `rules`, `emailLogs`, `ips`, `domains`, `badges`, `sso`,
`jobs`, `webhooks`, `integrations` (`->slack`), and `media` - matching the
[Console API](https://talk.hyvor.com/docs/api-console) one-to-one, plus `get()`/`update()` for the
website itself.

### Hyvor Post

```php
use Hyvor\Sdk\Post\PostClient;

$post = new PostClient(cloudApiKey: 'your-cloud-api-key');

// resource-level access to a specific newsletter (the cloud API key/token
// provider must have access to it)
$newsletter = $post->newsletter($newsletterId)->get();
$newsletter = $post->newsletter($newsletterId)->update(['name' => 'My Newsletter']);

$issues = $post->newsletter($newsletterId)->issues->list(['limit' => 10]);
$subscribers = $post->newsletter($newsletterId)->subscribers->list();
```

`$post->newsletter($newsletterId)` exposes: `issues`, `lists`, `subscribers`,
`subscriberMetadataDefinitions`, `sendingProfiles`, `templates`, `users`, `invites`, `media`, and
`exports` - matching the [Console API](https://post.hyvor.com/docs/api-console) one-to-one, plus
`get()`/`update()` for the newsletter itself.

Unlike Talk, Post has no org-level endpoints (there's no API to create a newsletter), so
`PostClient` only exposes `newsletter()`. Also unlike Talk, Post's Console API doesn't embed the
newsletter's ID in the URL - every request instead carries an `X-Newsletter-Id` header, which is
how an org-level cloud API key (otherwise valid for every newsletter the org can access) resolves
to one specific newsletter.

### Using both products together

`TalkClient` and `PostClient` are fully independent - each builds its own token provider (and thus
does its own JWT exchange) when constructed with `cloudApiKey`. If you use both products and want
a single shared token/JWT instead of one per client, build a `TokenProviderInterface` once and pass
it to both:

```php
use Hyvor\Sdk\Auth\CloudApiKeyTokenProvider;
use Hyvor\Sdk\Post\PostClient;
use Hyvor\Sdk\Talk\TalkClient;

$tokenProvider = new CloudApiKeyTokenProvider('your-cloud-api-key', ...);

$talk = new TalkClient(tokenProvider: $tokenProvider);
$post = new PostClient(tokenProvider: $tokenProvider);
```

### Resource-level API keys

Resource-level API keys are generated in the Console of each product and are scoped to a
single resource (e.g. one website). They can be used without any client-level auth:

```php
$talk = new TalkClient();

$website = $talk->website($websiteId, 'your-product-api-key')->get();

// org-level endpoints (like $talk->websites->create()) are not supported
// this way, since resource-level API keys are scoped to a single resource.
```

### Configuration

```php
$talk = new TalkClient(
    cloudApiKey: '...',                          // or tokenProvider: ...
    productUrl: null,                            // self-hosted override, see below
    logger: $psrLogger,                          // PSR-3 logger, default NullLogger
    httpClient: $psr18Client,                    // Psr\Http\Client\ClientInterface, default: auto-discovered
    requestFactory: $psr17Factory,               // Psr\Http\Message\RequestFactoryInterface, default: auto-discovered
    streamFactory: $psr17Factory,                // Psr\Http\Message\StreamFactoryInterface, default: auto-discovered
    retryMaxAttempts: 3,
    retryBackoffFactor: 2.0,
    cloudInstance: 'https://hyvor.com',          // default; hyvor.com-hosted (cloud) use only, see below
);
```

`PostClient` accepts the same parameters.

### Self-hosted instances

By default, requests go to `https://{product}.hyvor.com` (derived from `cloudInstance`, which you
normally never need to set - it only matters for hyvor.com-hosted/cloud usage). If you run a
self-hosted instance, set `productUrl` instead to point the client directly at it, bypassing the
`cloudInstance`-derived URL entirely:

```php
$talk = new TalkClient(
    tokenProvider: new StaticTokenProvider('your-jwt'), // cloudApiKey/cloud token exchange isn't available self-hosted
    productUrl: 'https://talk.example.com',
);
```

`cloudApiKey` isn't supported for self-hosted instances (it depends on hyvor.com for the JWT
exchange) - use `tokenProvider` instead.

### Authentication

`TalkClient`/`PostClient` accept at most one of (both are optional - see resource-level API keys
above):

- `cloudApiKey` - a Cloud API key created at `https://hyvor.com/account/org/api-keys`. The SDK
  exchanges it for a short-lived JWT internally (and refreshes it as needed).
- `tokenProvider` - a `Hyvor\Sdk\Auth\TokenProviderInterface` implementation for full control over
  how the bearer token is obtained. `Hyvor\Sdk\Auth\StaticTokenProvider` is included for the common
  case of using a single, pre-issued token (e.g. a JWT generated by an internal integration):

```php
use Hyvor\Sdk\Auth\StaticTokenProvider;

$talk = new TalkClient(
    tokenProvider: new StaticTokenProvider('your-jwt'),
);
```

### Request options

Per-request overrides (retries, extra headers):

```php
use Hyvor\Sdk\RequestOptions;

$talk->websites->create(
    new CreateWebsiteRequest(name: 'My Blog', domain: 'blog.example.com'),
    new RequestOptions(retryMaxAttempts: 1),
);
```

### Acting as a specific moderator

By default, the Console API is authenticated as the website owner. To act as a different
moderator (see the Console API's "User Authentication" docs), set `X-AUTH-USER-EMAIL` or
`X-AUTH-USER-SSO-ID` - either as a default for every call made through a `WebsiteClient` and its
sub-resources:

```php
$website = $talk->website($websiteId, headers: ['X-AUTH-USER-EMAIL' => 'mod@example.com']);
$website->comments->reply($commentId, new ReplyToCommentRequest(body: 'Thanks!'));
```

or per call, via `RequestOptions::$headers` (overrides the client-level default for that call):

```php
$website->comments->reply(
    $commentId,
    new ReplyToCommentRequest(body: 'Thanks!'),
    new RequestOptions(headers: ['X-AUTH-USER-EMAIL' => 'mod@example.com']),
);
```

### Errors

All API errors extend `Hyvor\Sdk\Exceptions\HyvorApiException`:

- `ValidationFailedException` (422) - has `$errors` (field => messages)
- `RateLimitException` (429) - has `$retryAfterSeconds`
- `AuthenticationException` (401/403)
- `NotFoundException` (404)
- `ServerErrorException` (5xx)
- `NetworkException` - request could not be sent (connection/timeout, from the underlying PSR-18 client)
- `ApiException` - fallback for other error statuses

Requests to `RateLimitException` and `ServerErrorException`-triggering statuses are retried automatically with exponential backoff before the exception is thrown.

## Testing

`hyvor/sdk-core` ships two helpers for testing code that uses the SDK, both under
`Hyvor\Sdk\Testing`:

- `FakeHttpClient` - a PSR-18 client you pass as `httpClient:` when constructing `TalkClient`/
  `PostClient` in a test. Queue responses (or exceptions) up front with `queueResponse()`/
  `queueException()`, then make calls against the client; every request it sent is recorded in
  `$http->requests` for assertions.
- `DtoFixtures::make(SomeDto::class, overrides: [...])` - builds a JSON-shaped array for a
  response DTO, filling every field you don't override with a type-appropriate default (`null`
  for nullable fields, `''`/`0`/`false`/`[]` for scalars). Use it to build the array you hand to
  `FakeHttpClient::queueResponse()` so a test only has to specify the fields it actually cares
  about, instead of every property the DTO happens to have.

```php
use Hyvor\Sdk\Talk\Dto\Website;
use Hyvor\Sdk\Talk\TalkClient;
use Hyvor\Sdk\Testing\DtoFixtures;
use Hyvor\Sdk\Testing\FakeHttpClient;
use Nyholm\Psr7\Response;

$http = new FakeHttpClient();
$http->queueResponse(new Response(201, [], json_encode(
    DtoFixtures::make(Website::class, ['name' => 'My Blog']),
)));

$talk = new TalkClient(tokenProvider: new StaticTokenProvider('test-jwt'), httpClient: $http);
$website = $talk->websites->create(new CreateWebsiteRequest(name: 'My Blog', domain: 'blog.example.com'));

// $http->requests[0] is the PSR-7 RequestInterface that was sent
```

## Development

See [DEV.md](../DEV.md) at the repo root for running tests (with or without Docker).

## Notes

- Talk requests are sent directly to the product's own instance, derived from `cloudInstance` by
  prefixing its host with the product name - e.g. `cloudInstance: 'https://hyvor.com'` (the
  default) resolves to `https://talk.hyvor.com` - unless `productUrl` is set, which is used as-is
  instead (see Self-hosted instances above). Both org-level endpoints (like
  `$talk->websites->create()`) and resource-level ones (everything under
  `$talk->website($id)`) go through this same per-product instance.
- A `cloudApiKey` is exchanged for a short-lived JWT via `POST {cloudInstance}/api/cloud/token`,
  then sent as `Authorization: Bearer <jwt>` on Talk requests. A resource-level API key (passed to
  `$talk->website($id, $apiKey)`) is sent the same way, as a bearer token, without any
  token exchange - even though the public Console API docs only document `X-API-KEY` auth for
  resource-level keys.

DTO fields mirror the publicly documented
[Talk Console API](https://talk.hyvor.com/docs/api-console) one-to-one, except: request DTOs treat
a `null` property as "omit this field" (so partial updates and list filters don't need every
property set) rather than sending a literal JSON `null` - the one documented exception is
`VoteOnCommentRequest::$type`, where `null` is itself a meaningful instruction (remove the vote),
so it's always sent verbatim.

- Post's Console API paths (unlike Talk's) don't embed any resource ID - `GET /issues`, not
  `GET /{newsletterId}/issues`. `$post->newsletter($id)` instead sends `X-Newsletter-Id: $id`
  as a default header on every request made through it and its sub-resources, which is what lets an
  org-level cloud API key (otherwise valid for every newsletter the org can access) resolve to one
  specific newsletter. DTO fields otherwise mirror the publicly documented
  [Post Console API](https://post.hyvor.com/docs/api-console) one-to-one, with the same
  null-means-omit convention as Talk.
