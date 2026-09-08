<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Relay;

use Hyvor\Sdk\Auth\StaticTokenProvider;
use Hyvor\Sdk\Relay\RelayClient;
use Hyvor\Sdk\Testing\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\RelayTestCase;
use Nyholm\Psr7\Factory\Psr17Factory;

final class ProjectTest extends RelayTestCase
{
    public function testGetReturnsProject(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleProject());

        $project = $this->client($http)->project(self::PROJECT_ID)->get();

        self::assertSame(1, $project->id);
        self::assertSame('My Project', $project->name);
        self::assertSame('transactional', $project->send_type->value);

        $request = $http->requests[0];
        self::assertSame('GET', $request->getMethod());
        self::assertSame($this->baseUrl() . '/project', (string) $request->getUri());
        self::assertSame('Bearer test-jwt-token', $request->getHeaderLine('Authorization'));
        self::assertSame('42', $request->getHeaderLine('X-Project-Id'));
    }

    public function testGetWithResourceApiKeyOverridesAuth(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleProject());

        $factory = new Psr17Factory();
        $client = new RelayClient(
            httpClient: $http,
            requestFactory: $factory,
            streamFactory: $factory,
        );

        $project = $client->project(self::PROJECT_ID, 'resource-api-key')->get();

        self::assertSame(1, $project->id);
        self::assertSame($this->baseUrl() . '/project', (string) $http->requests[0]->getUri());
        self::assertSame('Bearer resource-api-key', $http->requests[0]->getHeaderLine('Authorization'));
        self::assertSame('42', $http->requests[0]->getHeaderLine('X-Project-Id'));
    }

    public function testGetWithCustomHeadersOverridesProjectId(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleProject());

        $client = $this->client($http);
        $client->project(self::PROJECT_ID, headers: ['X-Project-Id' => '99'])->get();

        self::assertSame('99', $http->requests[0]->getHeaderLine('X-Project-Id'));
    }

    public function testUpdateSendsOnlyProvidedFields(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleProject(['name' => 'Renamed']));

        $project = $this->client($http)->project(self::PROJECT_ID)->update(['name' => 'Renamed']);

        self::assertSame('Renamed', $project->name);

        $request = $http->requests[0];
        self::assertSame('PATCH', $request->getMethod());
        self::assertSame($this->baseUrl() . '/project', (string) $request->getUri());
        self::assertSame(['name' => 'Renamed'], json_decode((string) $request->getBody(), true));
    }

    public function testCreateSendsNameAndSendType(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, [
            'id' => 5,
            'created_at' => 1700000000,
            'scopes' => ['project:read', 'project:write'],
            'user' => [
                'id' => 1,
                'name' => 'Jane',
                'email' => 'jane@example.com',
                'username' => null,
                'picture_url' => null,
                'oidc_sub' => null,
            ],
            'oidc_sub' => null,
            'project' => $this->sampleProject(['id' => 5, 'name' => 'New Project']),
        ]);

        $projectUser = $this->client($http)->org->projects->create([
            'name' => 'New Project',
            'send_type' => 'transactional',
        ]);

        self::assertSame(5, $projectUser->project->id);
        self::assertSame('New Project', $projectUser->project->name);

        $request = $http->requests[0];
        self::assertSame('POST', $request->getMethod());
        self::assertSame($this->baseUrl() . '/project', (string) $request->getUri());
        self::assertSame(
            ['name' => 'New Project', 'send_type' => 'transactional'],
            json_decode((string) $request->getBody(), true),
        );
        // org endpoint: no X-Project-Id header
        self::assertSame('', $request->getHeaderLine('X-Project-Id'));
        self::assertSame('Bearer test-jwt-token', $request->getHeaderLine('Authorization'));
    }

    public function testProductUrlOverridesCloudInstanceDerivedUrl(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleProject());

        $factory = new Psr17Factory();
        $relay = new RelayClient(
            httpClient: $http,
            requestFactory: $factory,
            streamFactory: $factory,
            tokenProvider: new StaticTokenProvider('test-jwt-token'),
            productUrl: 'https://relay.example.com',
        );

        $relay->project(self::PROJECT_ID)->get();

        $request = $http->requests[0];
        self::assertSame('https://relay.example.com/api/console/project', (string) $request->getUri());
    }
}
