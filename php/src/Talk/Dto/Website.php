<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto;

/**
 * Represents a Hyvor Talk website.
 */
final class Website
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $domain,
        public readonly ?\DateTimeImmutable $createdAt = null,
        public readonly ?\DateTimeImmutable $updatedAt = null,
    ) {
    }

    /**
     * @param array<mixed> $data
     * @internal
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            name: (string) $data['name'],
            domain: (string) $data['domain'],
            createdAt: isset($data['created_at']) ? new \DateTimeImmutable((string) $data['created_at']) : null,
            updatedAt: isset($data['updated_at']) ? new \DateTimeImmutable((string) $data['updated_at']) : null,
        );
    }
}
