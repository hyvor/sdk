<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Product\Talk\Dto\Website;

use Hyvor\Sdk\Product\Talk\Dto\Domain\Domain;
use Hyvor\Sdk\Product\Talk\Dto\Moderator\Mod;

/**
 * The website entity embedded in a `Domain` or `Mod`'s `website` relation.
 */
final class Website
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        /** @var array<string, mixed>|null */
        public readonly ?array $metadata,
        /** @var Domain[] */
        public readonly array $domains,
        /** @var Mod[] */
        public readonly array $mods,
        public readonly int $ownerId,
        public readonly int $organizationId,
        public readonly string $createdAt,
        public readonly bool $blocked,
        public readonly bool $isBlocked,
        public readonly bool $deleted,
        public readonly bool $isDeleted,
        public readonly ?string $deletedAt,
        public readonly string $authType,
        public readonly ?string $ssoType,
        public readonly ?string $ssoPrivateKey,
        public readonly ?string $ssoStatelessLoginUrl,
        public readonly bool $ssoStatelessKeyless,
        public readonly bool $ssoStatelessIsKeyless,
        public readonly ?string $consoleApiKey,
        public readonly ?string $createdBySource,
    ) {
    }
}
