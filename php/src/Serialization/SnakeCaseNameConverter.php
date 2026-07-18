<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Serialization;

use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

/**
 * Converts between camelCase PHP property names and the API's snake_case
 * field names.
 *
 * Unlike Symfony's built-in CamelCaseToSnakeCaseNameConverter, this also
 * treats a letter/digit boundary as a word boundary (e.g. `reaction1` <->
 * `reaction_1`, `isReaction1On` <-> `is_reaction_1_on`), matching how the
 * Hyvor API names fields.
 *
 * @internal
 */
final class SnakeCaseNameConverter implements NameConverterInterface
{
    public function normalize(string $propertyName, ?string $class = null, ?string $format = null, array $context = []): string
    {
        $withDigitBoundary = preg_replace('/(?<=[a-zA-Z])(?=[0-9])/', '_', $propertyName);
        $withUpperBoundary = preg_replace('/(?<!^)(?<!_)([A-Z])/', '_$1', $withDigitBoundary);

        return strtolower($withUpperBoundary);
    }

    public function denormalize(string $propertyName, ?string $class = null, ?string $format = null, array $context = []): string
    {
        $parts = explode('_', $propertyName);
        $camelCase = array_shift($parts) ?? '';

        foreach ($parts as $part) {
            $camelCase .= ucfirst($part);
        }

        return $camelCase;
    }
}
