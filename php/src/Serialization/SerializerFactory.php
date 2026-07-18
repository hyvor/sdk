<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Serialization;

use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Builds the Symfony Serializer used to (de)normalize SDK DTOs to/from the
 * API's snake_case JSON shape.
 *
 * @internal
 */
final class SerializerFactory
{
    public static function create(): Serializer
    {
        return new Serializer([
            new BackedEnumNormalizer(),
            new ObjectNormalizer(nameConverter: new SnakeCaseNameConverter()),
        ]);
    }
}
