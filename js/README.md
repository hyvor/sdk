# @hyvor/sdk-{product}

Official JavaScript/TypeScript SDKs for HYVOR Products, published as one npm package per product
so you only install what you use.

## Install

```bash
npm install @hyvor/sdk-talk   # Hyvor Talk
npm install @hyvor/sdk-post   # Hyvor Post
```

Both packages depend on `@hyvor/sdk-core` (installed automatically) for the shared HTTP transport,
auth, and exceptions - you never install `@hyvor/sdk-core` yourself.

Requires Node >= 18 (or another runtime with a global `fetch`, ex: Deno, Bun, modern browsers).
Requests are sent via the global `fetch` by default - no bundled HTTP client - unless you inject
your own via the `httpClient` option.

## Usage

### Hyvor Talk

```ts
import { TalkClient } from '@hyvor/sdk-talk';

// org-level access, via a cloud API key
const talk = new TalkClient({
    cloudApiKey: 'your-cloud-api-key', // or tokenProvider: yourTokenProviderImplementation
});

// GET /api/console/v1/{id}/website - resource-level access to a specific website.
// The cloud API key/token provider must have access to this website.
const website = talk.website(websiteId);

// POST /api/console/v1/websites - org-level endpoint, not scoped to one website
const newWebsite = await talk.org.websites.create({
    name: 'My Blog',
    domain: 'blog.example.com',
});

// every Console API resource hangs off the website client, e.g.:
const domains = await talk.website(websiteId).domains.update({ domains: ['blog.example.com'] });
const mods = await talk.website(websiteId).mods.list();
```

### Hyvor Post

```ts
import { PostClient } from '@hyvor/sdk-post';

const post = new PostClient({ cloudApiKey: 'your-cloud-api-key' });

// resource-level access to a specific newsletter (the cloud API key/token
// provider must have access to it)
const newsletter = await post.newsletter(newsletterId).get();
const updated = await post.newsletter(newsletterId).update({ name: 'My Newsletter' });

const issues = await post.newsletter(newsletterId).issue.list();
const subscribers = await post.newsletter(newsletterId).subscriber.list();
```

Unlike Talk, Post has no org-level endpoints (there's no API to create a newsletter). Also unlike
Talk, Post's Console API doesn't embed the newsletter's ID in the URL - every request instead
carries an `X-Newsletter-Id` header, which is how an org-level cloud API key (otherwise valid for
every newsletter the org can access) resolves to one specific newsletter.

### Types

Request/response DTOs are namespaced under `Dto` rather than exported individually, so they don't
clutter the package's top-level API:

```ts
import { Dto, TalkClient } from '@hyvor/sdk-talk';

function printDomain(domain: Dto.Domain) {
    console.log(domain.domain);
}

// Dto also holds the real enum values (not just types), ex:
import { Dto as PostDto, PostClient } from '@hyvor/sdk-post';

const status: PostDto.IssueStatus = PostDto.IssueStatus.SENT;
```

### Using both products together

`TalkClient` and `PostClient` are fully independent - each builds its own token provider (and thus
does its own JWT exchange) when constructed with `cloudApiKey`. If you use both products and want
a single shared token/JWT instead of one per client, build a `TokenProvider` once and pass it to
both:

```ts
import { CloudApiKeyTokenProvider, FetchHttpClient, NullLogger } from '@hyvor/sdk-core';
import { PostClient } from '@hyvor/sdk-post';
import { TalkClient } from '@hyvor/sdk-talk';

const tokenProvider = new CloudApiKeyTokenProvider(
    'your-cloud-api-key',
    'https://hyvor.com',
    new FetchHttpClient(),
    new NullLogger(),
);

const talk = new TalkClient({ tokenProvider });
const post = new PostClient({ tokenProvider });
```

### Resource-level API keys

Resource-level API keys are generated in the Console of each product and are scoped to a single
resource (ex: one website). They can be used without any client-level auth:

```ts
const talk = new TalkClient();

const website = await talk.website(websiteId, 'your-product-api-key').get();

// org-level endpoints (like talk.org.websites.create()) are not supported this
// way, since resource-level API keys are scoped to a single resource.
```

### Configuration

```ts
const talk = new TalkClient({
    cloudApiKey: '...',                 // or tokenProvider: ...
    productUrl: null,                   // self-hosted override, see below
    logger: yourLogger,                 // Logger, default: NullLogger
    httpClient: yourHttpClient,         // HttpClient, default: fetch-based
    retryMaxAttempts: 3,
    retryBackoffFactor: 2.0,
    cloudInstance: 'https://hyvor.com', // default; hyvor.com-hosted (cloud) use only, see below
});
```

`PostClient` accepts the same options.

### Self-hosted instances

By default, requests go to `https://{product}.hyvor.com` (derived from `cloudInstance`, which you
normally never need to set - it only matters for hyvor.com-hosted/cloud usage). If you run a
self-hosted instance, set `productUrl` instead to point the client directly at it, bypassing the
`cloudInstance`-derived URL entirely:

```ts
import { StaticTokenProvider } from '@hyvor/sdk-core';

const talk = new TalkClient({
    tokenProvider: new StaticTokenProvider('your-jwt'), // cloudApiKey/cloud token exchange isn't available self-hosted
    productUrl: 'https://talk.example.com',
});
```

`cloudApiKey` isn't supported for self-hosted instances (it depends on hyvor.com for the JWT
exchange) - use `tokenProvider` instead.

### Authentication

`TalkClient`/`PostClient` accept at most one of (both are optional - see resource-level API keys
above):

- `cloudApiKey` - a Cloud API key created at `https://hyvor.com/account/org/api-keys`. The SDK
  exchanges it for a short-lived JWT internally (and refreshes it as needed).
- `tokenProvider` - a `TokenProvider` implementation for full control over how the bearer token is
  obtained. `StaticTokenProvider` (from `@hyvor/sdk-core`) is included for the common case of using
  a single, pre-issued token (ex: a JWT generated by an internal integration).

### Request options

Per-request overrides (retries, extra headers):

```ts
await talk.org.websites.create(
    { name: 'My Blog', domain: 'blog.example.com' },
    { retryMaxAttempts: 1 },
);
```

### Errors

All API errors extend `HyvorApiException` (from `@hyvor/sdk-core`):

- `ValidationFailedException` (422) - has `errors` (field => messages)
- `RateLimitException` (429) - has `retryAfterSeconds`
- `AuthenticationException` (401/403)
- `NotFoundException` (404)
- `ServerErrorException` (5xx)
- `NetworkException` - request could not be sent (connection/timeout, from the underlying `fetch`)
- `ApiException` - fallback for other error statuses

Requests to `RateLimitException`- and `ServerErrorException`-triggering statuses are retried
automatically with exponential backoff before the exception is thrown.

## Development

See [DEV.md](../DEV.md) at the repo root for running tests (with or without Docker).

## Notes

- Talk requests are sent directly to the product's own instance, derived from `cloudInstance` by
  prefixing its host with the product name - ex: `cloudInstance: 'https://hyvor.com'` (the default)
  resolves to `https://talk.hyvor.com` - unless `productUrl` is set, which is used as-is instead
  (see Self-hosted instances above). Both org-level endpoints (like `talk.org.websites.create()`)
  and resource-level ones (everything under `talk.website(id)`) go through this same per-product
  instance.
- A `cloudApiKey` is exchanged for a short-lived JWT via `POST {cloudInstance}/api/cloud/token`,
  then sent as `Authorization: Bearer <jwt>` on Talk requests. A resource-level API key (passed to
  `talk.website(id, apiKey)`) is sent the same way, as a bearer token, without any token exchange -
  even though the public Console API docs only document `X-API-KEY` auth for resource-level keys.
- Response DTOs are plain TS interfaces (not classes) - the API's JSON field names already match a
  DTO's property names one-to-one, so there's no runtime (de)normalization step, unlike the PHP
  SDK's reflection-based denormalizer.

DTO fields mirror the publicly documented
[Talk Console API](https://talk.hyvor.com/docs/api-console) /
[Post Console API](https://post.hyvor.com/docs/api-console) one-to-one, except: request DTOs treat
an absent property as "omit this field" (so partial updates and list filters don't need every
property set) - the same convention the PHP SDK uses.
