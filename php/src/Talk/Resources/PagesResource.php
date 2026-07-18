<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Resources;

use Hyvor\Sdk\Exceptions\HyvorApiException;
use Hyvor\Sdk\RequestOptions;
use Hyvor\Sdk\Talk\Dto\Page\CreatePageRequest;
use Hyvor\Sdk\Talk\Dto\Page\ListPagesRequest;
use Hyvor\Sdk\Talk\Dto\Page\Page;
use Hyvor\Sdk\Talk\Dto\Page\ResetPageDataRequest;
use Hyvor\Sdk\Talk\Dto\Page\UpdatePageRequest;

/**
 * `$client->talk->website($websiteId)->pages`
 */
final class PagesResource extends WebsiteScopedResource
{
    /**
     * GET /pages
     *
     * @return Page[]
     * @throws HyvorApiException
     */
    public function list(?ListPagesRequest $request = null, ?RequestOptions $options = null): array
    {
        $body = $this->transport->normalize($request ?? new ListPagesRequest());
        $data = $this->request('GET', $this->path('/pages'), $body, $options);

        return $this->transport->denormalizeList($data, Page::class);
    }

    /**
     * POST /page
     *
     * @throws HyvorApiException
     */
    public function create(CreatePageRequest $request, ?RequestOptions $options = null): Page
    {
        $body = $this->transport->normalize($request);
        $data = $this->request('POST', $this->path('/page'), $body, $options);

        return $this->transport->denormalize($data, Page::class);
    }

    /**
     * PATCH /page/{id}
     *
     * @param int|string $id By default the internal page ID. Pass
     *  `$byIdentifier: true` to instead resolve it as the `page-id`
     *  attribute set in the embed (sets the X-ID-Type header).
     * @throws HyvorApiException
     */
    public function update(
        int|string $id,
        UpdatePageRequest $request,
        bool $byIdentifier = false,
        ?RequestOptions $options = null,
    ): Page {
        $body = $this->transport->normalize($request);
        $data = $this->request('PATCH', $this->path("/page/{$id}"), $body, $options, $this->idTypeHeader($byIdentifier));

        return $this->transport->denormalize($data, Page::class);
    }

    /**
     * POST /page/{id}/reset
     *
     * Resets the page's data (comments/reactions/ratings). Use with caution
     * — there is no undo.
     *
     * @throws HyvorApiException
     */
    public function reset(
        int|string $id,
        ResetPageDataRequest $request,
        bool $byIdentifier = false,
        ?RequestOptions $options = null,
    ): Page {
        $body = $this->transport->normalize($request);
        $data = $this->request('POST', $this->path("/page/{$id}/reset"), $body, $options, $this->idTypeHeader($byIdentifier));

        return $this->transport->denormalize($data, Page::class);
    }

    /**
     * POST /page/{id}/move
     *
     * Moves all data from this page to another page. Useful when changing
     * the `page-id` attribute of the embed.
     *
     * @throws HyvorApiException
     */
    public function move(
        int|string $id,
        int $toPageId,
        bool $byIdentifier = false,
        ?RequestOptions $options = null,
    ): void {
        $this->request(
            'POST',
            $this->path("/page/{$id}/move"),
            ['to_page_id' => $toPageId],
            $options,
            $this->idTypeHeader($byIdentifier),
        );
    }

    /**
     * DELETE /page/{id}
     *
     * Deletes all data of the page (comments, reactions, ratings) and the
     * page itself. Use with caution — there is no undo.
     *
     * @throws HyvorApiException
     */
    public function delete(int|string $id, bool $byIdentifier = false, ?RequestOptions $options = null): void
    {
        $this->request('DELETE', $this->path("/page/{$id}"), null, $options, $this->idTypeHeader($byIdentifier));
    }

    /**
     * @return array<string, string>
     */
    private function idTypeHeader(bool $byIdentifier): array
    {
        return $byIdentifier ? ['X-ID-Type' => 'page_id'] : [];
    }
}
