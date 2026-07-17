<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Support;

use Hyvor\Sdk\Http\HttpClientInterface;
use Hyvor\Sdk\Http\HttpRequest;
use Hyvor\Sdk\Http\HttpResponse;
use Hyvor\Sdk\Http\HttpTransportException;

final class FakeHttpClient implements HttpClientInterface
{
    /** @var array<int, HttpResponse|\Throwable> */
    private array $queue = [];

    /** @var HttpRequest[] */
    public array $requests = [];

    public function queueResponse(HttpResponse $response): void
    {
        $this->queue[] = $response;
    }

    public function queueException(\Throwable $exception): void
    {
        $this->queue[] = $exception;
    }

    public function sendRequest(HttpRequest $request): HttpResponse
    {
        $this->requests[] = $request;

        $next = array_shift($this->queue);

        if ($next === null) {
            throw new HttpTransportException('FakeHttpClient: no queued response left.');
        }

        if ($next instanceof \Throwable) {
            throw $next;
        }

        return $next;
    }
}
