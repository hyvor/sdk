How to write an SDK:

## Client

First, we have a client:

```ts
// creating the client
const client = new HyvorClient({

    /**
     * This is a Cloud API key created at https://hyvor.com/account/org/api-keys by an admin of the organization.
     * Scopes are pre-defined. SDKs internally request a short-lived JWT token from hyvor.com.
     */
    cloudApiKey: "",

    /**
     * Can be used to set a custom instance
     * @default https://hyvor.com
     */
    cloudInstance: "",

    /**
     * A customizable, standard logger interface to use. 
     */
    logger: Logger,

    /**
     * A customizable, standard HTTP client interface to use.
     */
    httpClient: HttpClient,

    /**
     * a custom token provider
     */
    tokenProvider: TokenProvider, // getToken(): string

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

});
```

## Products

Then, client have product namespaces:

Product APIs to support

- (talk) Hyvor Talk: https://talk.hyvor.com/docs/api-console
- (blogs) Hyvor Blogs: https://blogs.hyvor.com/docs/api-console
- (post) Hyvor Post: https://post.hyvor.com/docs/api-console
- (relay) Hyvor Relay: https://relay.hyvor.com/docs/api-console

## Calling the API with a cloud API key

Cloud API keys are org-level, and created at https://hyvor.com/account/org/api-keys by an admin of the organization. This feature is NOT SUPPORTED in self-hosted instances (because it depends on hyvor.com for token exchange).

```ts
const client = new HyvorClient({
    cloudApiKey: "your-cloud-api-key",
});

/**
 * client.talk => Hyvor Talk product namespace
 * client.talk.for(websiteId) => Access a specific website by its ID
 * 
 * the cloud API key must have access to the given resource (website, blog, newsletter, etc.)
 */
const comments = client.talk.website(websiteId).comments.list();
const posts = client.blogs.blog(blogSubdomain).posts.list(); // for supports id or subdomain
const issues = client.post.newsletter(newsletterId).issues.list();
const sends = client.relay.project(projectId).sends.list();

/**
 * Calling an org-level endpoint
 */
const newWebsite = client.talk.websites.create({
    name: "My Blog",
    domain: "blog.example.com",
});
```

## Calling the API with a resource-level API key

Resource API keys are generated in the Console of each product.

```ts
const client = new HyvorClient();

const comments = client.talk.website(websiteId, "your-product-api-key").comments.list();
const posts = client.blogs.blog(blogSubdomain, "your-product-api-key").posts.list();
const issues = client.post.newsletter(newsletterId, "your-product-api-key").issues.list();
const sends = client.relay.project(projectId, "your-product-api-key").sends.list();

// org-level endpoints are not supported this way
// because resource-level API keys are scoped to a specific resource (website, blog, etc.)
```

## Rules

- set SDK name to `hyvor/sdk-{language}` (@hyvor for npm, and adjust for others)
- Github Actions based CI.
- All inputs and outputs should be typed.
- use typed DTOs for request and response objects.
  - when a field supports multiple types, prefer using the object type (ex: use an Address object instead of a string email address).
  - if the language supports it, use enums
- use list() for list endpoints, get() for single resource endpoints, create() for POST endpoints, update() for PUT/PATCH endpoints, delete() for DELETE endpoints.
- set the User-Agent header in API requests to hyvor/sdk-{language}/{version} (ex: hyvor/sdk-php/1.0.0)
- support injecting a logger and HTTP client for testing, mocking, and debugging.
- return (ex: Rust) or throw (ex: PHP) custom errors (ValidationFailedError, ServerError, RateLimits) that can be used to handle errors gracefully.
- resource should be a resource name, like `comments`, `posts`, `users`, etc. Always plural, even if the API uses singular.
  - except for the main resource (`website` in talk, `blog` in blogs, `newsletter` in post)
- Dockerfile should have a stage for running the SDK tests, compose.yaml should provide mounts, etc. and should document testing steps in DEV.md