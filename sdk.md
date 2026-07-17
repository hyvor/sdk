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

For now:

- `talk`: Hyvor Talk
- `post`: Hyvor Post

```ts
client.talk.resource.method();
client.post.resource.method();
```

Endpoints to support:

- Hyvor Talk: https://talk.hyvor.com/docs/api-console
- Hyvor Post: https://post.hyvor.com/docs/api-console

```ts
client.talk.comments.create(
    {
        // request body
    },
    {
        // request options (override default timeouts, retry config etc.)
        // some endpoints support idempotency keys, which can be set here.
    }
);
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