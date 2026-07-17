<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Support;

use Psr\Http\Client\ClientExceptionInterface;

final class FakeHttpTransportException extends \RuntimeException implements ClientExceptionInterface
{
}
