<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Serialization;

use Hyvor\Sdk\Talk\Dto\User\CommentingUser;
use Hyvor\Sdk\Talk\Dto\User\GuestUser;
use Hyvor\Sdk\Talk\Dto\User\LoggedInUser;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Denormalizes a `CommentingUser` to either a `LoggedInUser` or a
 * `GuestUser`, based on the API's `type` field (null => guest).
 *
 * @internal
 */
final class CommentingUserDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): CommentingUser
    {
        /** @var array<string, mixed> $data */
        $targetClass = ($data['type'] ?? null) === null ? GuestUser::class : LoggedInUser::class;

        /** @var CommentingUser */
        return $this->denormalizer->denormalize($data, $targetClass, $format, $context);
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === CommentingUser::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [CommentingUser::class => true];
    }
}
