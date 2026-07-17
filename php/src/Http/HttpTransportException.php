<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Http;

/**
 * Thrown by HttpClientInterface implementations when a request could not be
 * sent at all (connection refused, DNS failure, timeout, etc), as opposed to
 * the server responding with an HTTP error status.
 */
final class HttpTransportException extends \RuntimeException
{
}
