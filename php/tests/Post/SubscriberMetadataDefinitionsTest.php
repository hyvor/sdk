<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Post;

use Hyvor\Sdk\Post\Dto\SubscriberMetadataDefinition\CreateSubscriberMetadataDefinitionRequest;
use Hyvor\Sdk\Post\Dto\SubscriberMetadataDefinition\UpdateSubscriberMetadataDefinitionRequest;
use Hyvor\Sdk\Tests\Support\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\PostTestCase;

final class SubscriberMetadataDefinitionsTest extends PostTestCase
{
    public function testCreate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [
            'id' => 1,
            'created_at' => 1700000000,
            'key' => 'favorite_color',
            'name' => 'Favorite Color',
            'type' => 'text',
        ], 201);

        $definition = $this->client($http)->post->newsletter(self::NEWSLETTER_ID)->subscriberMetadataDefinitions->create(
            new CreateSubscriberMetadataDefinitionRequest(key: 'favorite_color', name: 'Favorite Color'),
        );

        self::assertSame('favorite_color', $definition->key);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/subscriber-metadata-definitions', (string) $request->getUri());
        self::assertSame(
            ['key' => 'favorite_color', 'name' => 'Favorite Color'],
            json_decode((string) $request->getBody(), true),
        );
    }

    public function testUpdate(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [
            'id' => 1,
            'created_at' => 1700000000,
            'key' => 'favorite_color',
            'name' => 'Renamed',
            'type' => 'text',
        ]);

        $definition = $this->client($http)->post->newsletter(self::NEWSLETTER_ID)->subscriberMetadataDefinitions->update(
            1,
            new UpdateSubscriberMetadataDefinitionRequest(name: 'Renamed'),
        );

        self::assertSame('Renamed', $definition->name);
        self::assertSame($this->baseUrl() . '/subscriber-metadata-definitions/1', (string) $http->requests[0]->getUri());
    }

    public function testDelete(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, []);

        $this->client($http)->post->newsletter(self::NEWSLETTER_ID)->subscriberMetadataDefinitions->delete(1);

        $request = $http->requests[0];
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame($this->baseUrl() . '/subscriber-metadata-definitions/1', (string) $request->getUri());
    }
}
