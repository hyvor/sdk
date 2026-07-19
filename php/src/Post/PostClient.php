<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post;

use Hyvor\Sdk\Http\Transport;
use Hyvor\Sdk\Post\Resources\OrgNewslettersResource;

/**
 * Namespace for the Hyvor Post product, accessible via `$client->post`.
 */
final class PostClient
{
    /**
     * Org-level access to newsletters, accessible via `$client->post->newsletters`.
     */
    public readonly OrgNewslettersResource $newsletters;

    public function __construct(private readonly Transport $transport)
    {
        $this->newsletters = new OrgNewslettersResource($this->transport);
    }

    /**
     * Resource-level access to a single newsletter.
     *
     * @param int|string $newsletterId The newsletter's ID.
     * @param string|null $apiKey A resource-level API key scoped to this
     *  newsletter. If omitted, the client's org-level auth is used instead.
     * @param array<string, string> $headers Default headers merged into
     *  every request made through the returned client (and its
     *  sub-resources). Can be overridden per-call via
     *  `RequestOptions::$headers`.
     */
    public function newsletter(int|string $newsletterId, ?string $apiKey = null, array $headers = []): NewsletterClient
    {
        return new NewsletterClient($this->transport, $newsletterId, $apiKey, $headers);
    }
}
