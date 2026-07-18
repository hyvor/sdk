<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Serialization;

use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Builds the Symfony Serializer used to (de)normalize SDK DTOs to/from the
 * API's JSON shape. DTO properties are named identically to the API's JSON
 * fields (snake_case), so no name converter is needed.
 *
 * @internal
 */
final class SerializerFactory
{
    public static function create(): Serializer
    {
        return new Serializer([
            new BackedEnumNormalizer(),
            new ObjectNormalizer(),
        ]);
    }
}
