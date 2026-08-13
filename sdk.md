How to write an SDK:

## Client

Each product gets its own package and its own client class, constructed directly - there is no
unified `HyvorClient` facade spanning every product. This keeps installs small: a consumer who
only uses Talk never pulls in Post's (or Blogs'/Relay's) DTOs and resources. Every language's SDK
follows this split (see PHP under Rules below for the concrete package/namespace layout PHP
uses - other languages should mirror the same idea, adapted to that language's packaging norms).

First, we have a client:

```ts
// creating the client - one class per product, e.g. TalkClient from "@hyvor/sdk-talk"
const talk = new TalkClient({

    /**
     * This is a Cloud API key created at https://hyvor.com/account/org/api-keys by an admin of the organization.
     * Scopes are pre-defined. SDKs internally request a short-lived JWT token from hyvor.com.
     */
    cloudApiKey: "",

    /**
     * a custom token provider
     */
    tokenProvider: TokenProvider, // getToken(): string

    /**
     * Overrides the product URL derived from cloudInstance. Set this for a
     * self-hosted instance (e.g. "https://talk.example.com") to bypass
     * *.hyvor.com entirely. See "Self-hosted instances" below.
     */
    productUrl: "",

    /**
     * A customizable, standard logger interface to use. 
     */
    logger: Logger,

    /**
     * A customizable, standard HTTP client interface to use.
     */
    httpClient: HttpClient,

    /**
     * The maximum time, in milliseconds, to wait for a connection
     * (response headers) to be established.
     * @default 5000
     */
    connectTimeoutMs: 5000,

    /**
     * The maximum time, in milliseconds, to wait for the entire
     * request (including reading the response body) to complete.
     * @default 10000
     */
    requestTimeoutMs: 10000,

    /**
     * The maximum number of attempts (including the first) before
     * giving up.
     */
    retryMaxAttempts: 3,

    /**
     * The multiplier applied to the base delay between each retry
     * (exponential backoff).
     */
    retryBackoffFactor: 2.0, // exponential backoff factor

    /**
     * Can be used to set a custom cloud instance. Only relevant for
     * hyvor.com-hosted (cloud) usage - end users essentially never set this;
     * self-hosted users should set productUrl instead. Listed last since
     * it's the option least likely to ever be touched.
     * @default https://hyvor.com
     */
    cloudInstance: "",

});
```

## Products

Each product below is its own package with its own client class:

Product APIs to support

- talk
  - name: Hyvor Talk
  - url: https://talk.hyvor.com
  - docs: https://talk.hyvor.com/docs/api-console
- blogs
  - name: Hyvor Blogs
  - url: https://blogs.hyvor.com
  - docs: https://blogs.hyvor.com/docs/api-console
- post
  - name: Hyvor Post
  - url: https://post.hyvor.com
  - docs: https://post.hyvor.com/docs/api-console
- relay
  - name: Hyvor Relay
  - url: https://relay.hyvor.com
  - docs: https://relay.hyvor.com/docs/api-console

## Calling the API with a cloud API key

Cloud API keys are org-level, and created at https://hyvor.com/account/org/api-keys by an admin of the organization. This feature is NOT SUPPORTED in self-hosted instances (because it depends on hyvor.com for token exchange).

```ts
const talk = new TalkClient({ cloudApiKey: "your-cloud-api-key" });
const blogs = new BlogsClient({ cloudApiKey: "your-cloud-api-key" });
const post = new PostClient({ cloudApiKey: "your-cloud-api-key" });
const relay = new RelayClient({ cloudApiKey: "your-cloud-api-key" });

/**
 * talk.website(websiteId) => Access a specific website by its ID
 *
 * the cloud API key must have access to the given resource (website, blog, newsletter, etc.)
 */
const comments = talk.website(websiteId).comments.list();
const posts = blogs.blog(blogSubdomain).posts.list(); // for supports id or subdomain
const issues = post.newsletter(newsletterId).issues.list();
const sends = relay.project(projectId).sends.list();

/**
 * Calling an org-level endpoint
 */
const newWebsite = talk.websites.create({
    name: "My Blog",
    domain: "blog.example.com",
});
```

Using more than one product at once? Each client builds its own token provider (and does its own
JWT exchange) by default. To share a single token/JWT instead of one per client, build a
`TokenProvider` once and pass it to each client's `tokenProvider` option instead of `cloudApiKey`.

## Calling the API with a resource-level API key

Resource API keys are generated in the Console of each product.

```ts
const talk = new TalkClient();
const blogs = new BlogsClient();
const post = new PostClient();
const relay = new RelayClient();

const comments = talk.website(websiteId, "your-product-api-key").comments.list();
const posts = blogs.blog(blogSubdomain, "your-product-api-key").posts.list();
const issues = post.newsletter(newsletterId, "your-product-api-key").issues.list();
const sends = relay.project(projectId, "your-product-api-key").sends.list();

// org-level endpoints are not supported this way
// because resource-level API keys are scoped to a specific resource (website, blog, etc.)
```

## Self-hosted instances

By default, requests go to `https://{product}.hyvor.com` (derived from `cloudInstance`). Self-hosted
instances aren't reachable this way, so every client also accepts `productUrl`, which overrides
the `cloudInstance`-derived URL entirely:

```ts
const talk = new TalkClient({
    tokenProvider: yourTokenProvider, // cloudApiKey isn't supported self-hosted
    productUrl: "https://talk.example.com",
});
```

## Rules

### General

- Package/registry name: `hyvor/sdk-{product}` (`@hyvor/sdk-{product}` for npm) - one published package per product, per language. No language segment in the package name itself, since the registry (Packagist, npm, PyPI, ...) is already language-scoped.
- Standalone repo name (for languages that sync a monorepo subdirectory out to its own repo for publishing, as PHP does): `hyvor/sdk-{language}-{product}` (ex: `hyvor/sdk-php-talk`), and `hyvor/sdk-{language}-core` for that language's shared/core package (ex: `hyvor/sdk-php-core`). The language segment goes here (not in the package name) because GitHub, unlike the package registries, isn't language-scoped - the `hyvor` org hosts one repo per language+product combination.
- No unified client: each product is its own package with its own directly-constructed client class (`TalkClient`, `PostClient`, ...) - see "Client" above.
- Github Actions based CI.
- All inputs and outputs should be typed.
- use typed DTOs for request and response objects.
  - when a field supports multiple types, prefer using the object type (ex: use an Address object instead of a string email address).
  - if the language supports it, use enums
- use list() for list endpoints, get() for single resource endpoints, create() for POST endpoints, update() for PUT/PATCH endpoints, delete() for DELETE endpoints.
- set the User-Agent header in API requests to `hyvor/sdk-{language}-{product}/{version}` (ex: `hyvor/sdk-php-talk/1.0.0`)
- support injecting a logger and HTTP client for testing, mocking, and debugging.
- return (ex: Rust) or throw (ex: PHP) custom errors (ValidationFailedError, ServerError, RateLimits) that can be used to handle errors gracefully.
- resource should be a resource name, like `comments`, `posts`, `users`, etc. Always plural, even if the API uses singular.
  - except for the main resource (`website` in talk, `blog` in blogs, `newsletter` in post)
- Dockerfile should be configured for running the SDK tests, compose.yaml should provide mounts, etc. and should document testing steps in DEV.md
- testing
  - all endpoints should be tested with mock HTTP responses
  - abstract commonly used methods into a base test class or helper functions

### OpenAPI

- All clients should be generated from OpenAPI specs (./openapi/*.json).
- Use metadata (descriptions, etc.) from OpenAPI spec to generate doc comments in the SDK. Avoid adding extra comments that are not in the OpenAPI spec.
- Org-level endpoints have a `org-endpoint` tag.

### PHP

The PHP SDK is split into one Composer package per product, plus a shared internal core package -
not one monolithic `hyvor/sdk` package. This keeps installs small: a consumer who only uses Talk
never pulls in Post's DTOs (or Blogs'/Relay's, once those exist).

Packages (each `php/packages/<dir>` in the monorepo):

- `hyvor/sdk-core` (`packages/core/`) - Auth, Exceptions, Http (incl. `TransportBuilder`,
  `Transport`, `ProductBaseUrl`), Serialization, `RequestOptions`, `Version`, and the shipped
  test-mocking helpers under `Testing/` (`FakeHttpClient`, `FakeHttpTransportException`). No
  product knowledge. Installed transitively as a dependency of every product package - never
  required directly by consumers. Published as the standalone repo `hyvor/sdk-php-core`.
- `hyvor/sdk-talk` (`packages/talk/`), `hyvor/sdk-post` (`packages/post/`), and (once
  implemented) `hyvor/sdk-blogs`, `hyvor/sdk-relay` - one per product, each requiring
  `hyvor/sdk-core`. Published as the standalone repos `hyvor/sdk-php-talk`, `hyvor/sdk-php-post`,
  etc.

Folder structure, per product package (`packages/talk/` shown; `packages/post/` mirrors it):

```
packages/talk/
  composer.json       # hyvor/sdk-talk, requires hyvor/sdk-core
  src/Talk/
    Dto/               (for response DTOs)
    Website/           (website-level resources)
    Org/                (org-level resources)
    WebsiteClient
    TalkClient          (constructed directly: new TalkClient(cloudApiKey: ..., productUrl: ...))
  tests/Talk/
```

And the shared core package:

```
packages/core/
  composer.json       # hyvor/sdk-core
  src/
    Auth/ (for token provider, etc.)
    Exceptions/ (for custom exceptions)
    Http/ (transport, incl. TransportBuilder - the wiring every product client's
           constructor delegates to)
    Serialization/
    Testing/ (FakeHttpClient, etc. - shipped so product packages' own test suites can use them)
    RequestOptions
    Version
  tests/
```

There is no `HyvorClient` facade and no `Product/` namespace segment - each product's client is
the top-level entry point for that package (`Hyvor\Sdk\Talk\TalkClient`, not
`Hyvor\Sdk\Product\Talk\TalkClient`), constructed directly with the same config parameters
(`cloudApiKey`, `tokenProvider`, `productUrl`, `logger`, `httpClient`, ..., `cloudInstance` last -
matching the general parameter ordering above). The root `php/composer.json` is a dev-only
aggregator (using Composer path repositories) that installs all packages from disk for
whole-monorepo testing - it is not published.

- for inputs, use typed array inputs (phpstan-friendly array shapes). e.g. `$talk->websites->create(['name' => 'My Blog']);`.
  - if inputs have enums, use string-literal unions in the array shape (e.g. `'newest'|'oldest'|...`), not enum classes
- for outputs, use DTO classes.
  - in outputs, use enums for closed value sets.
