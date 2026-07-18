<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Tests\Post;

use Hyvor\Sdk\Exceptions\ValidationFailedException;
use Hyvor\Sdk\HyvorClient;
use Hyvor\Sdk\Tests\Support\FakeHttpClient;
use Hyvor\Sdk\Tests\Support\PostTestCase;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;

final class NewsletterTest extends PostTestCase
{
    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function sampleNewsletterData(array $overrides = []): array
    {
        return array_merge([
            'id' => 'nl_42',
            'subdomain' => 'my-newsletter',
            'created_at' => 1700000000,
            'name' => 'My Newsletter',

            'address' => null,
            'unsubscribe_text' => null,
            'branding' => true,

            'template_color_accent' => null,
            'template_color_accent_text' => null,
            'template_color_background' => null,
            'template_color_background_text' => null,
            'template_color_box' => null,
            'template_color_box_text' => null,

            'template_box_shadow' => null,
            'template_box_radius' => null,
            'template_box_border' => null,

            'template_font_family' => null,
            'template_font_size' => null,
            'template_font_weight' => null,
            'template_font_weight_heading' => null,
            'template_font_line_height' => null,

            'form_title' => null,
            'form_description' => null,
            'form_footer_text' => null,
            'form_button_text' => null,
            'form_success_message' => null,

            'form_width' => null,
            'form_custom_css' => null,

            'form_color_light_text' => null,
            'form_color_light_text_light' => null,
            'form_color_light_accent' => null,
            'form_color_light_accent_text' => null,
            'form_color_light_input' => null,
            'form_color_light_input_text' => null,
            'form_light_input_box_shadow' => null,
            'form_light_input_border' => null,
            'form_light_border_radius' => null,

            'form_color_dark_text' => null,
            'form_color_dark_text_light' => null,
            'form_color_dark_accent' => null,
            'form_color_dark_accent_text' => null,
            'form_color_dark_input' => null,
            'form_color_dark_input_text' => null,
            'form_dark_input_box_shadow' => null,
            'form_dark_input_border' => null,
            'form_dark_border_radius' => null,

            'form_default_color_palette' => 'os',
            'form_input_border_radius' => 4,
        ], $overrides);
    }

    public function testGetReturnsNewsletter(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleNewsletterData());

        $newsletter = $this->client($http)->post->newsletter(self::NEWSLETTER_ID)->get();

        self::assertSame('nl_42', $newsletter->id);
        self::assertSame('My Newsletter', $newsletter->name);
        self::assertSame(
            \Hyvor\Sdk\Post\Dto\Newsletter\FormColorPalette::OS,
            $newsletter->form_default_color_palette,
        );

        $request = $http->requests[0];
        self::assertSame('GET', $request->getMethod());
        self::assertSame($this->baseUrl() . '/newsletter', (string) $request->getUri());
        self::assertSame('Bearer test-jwt-token', $request->getHeaderLine('Authorization'));
        self::assertSame('nl_42', $request->getHeaderLine('X-Newsletter-Id'));
    }

    public function testGetWithResourceApiKeyOverridesAuth(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleNewsletterData());

        $factory = new Psr17Factory();
        $client = new HyvorClient(
            httpClient: $http,
            requestFactory: $factory,
            streamFactory: $factory,
        );

        $newsletter = $client->post->newsletter(self::NEWSLETTER_ID, 'resource-api-key')->get();

        self::assertSame('nl_42', $newsletter->id);
        self::assertSame($this->baseUrl() . '/newsletter', (string) $http->requests[0]->getUri());
        self::assertSame('Bearer resource-api-key', $http->requests[0]->getHeaderLine('Authorization'));
        self::assertSame('nl_42', $http->requests[0]->getHeaderLine('X-Newsletter-Id'));
    }

    public function testGetWithCustomHeadersOverridesNewsletterId(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleNewsletterData());

        $client = $this->client($http);
        $client->post->newsletter(self::NEWSLETTER_ID, headers: ['X-Newsletter-Id' => 'override'])->get();

        self::assertSame('override', $http->requests[0]->getHeaderLine('X-Newsletter-Id'));
    }

    public function testUpdateSendsOnlyProvidedFields(): void
    {
        $http = new FakeHttpClient();
        $this->queueJson($http, $this->sampleNewsletterData(['name' => 'Renamed']));

        $client = $this->client($http);
        $newsletter = $client->post->newsletter(self::NEWSLETTER_ID)->update(
            ['name' => 'Renamed'],
        );

        self::assertSame('Renamed', $newsletter->name);

        $request = $http->requests[0];
        self::assertSame('PATCH', $request->getMethod());
        self::assertSame($this->baseUrl() . '/newsletter', (string) $request->getUri());
        self::assertSame(['name' => 'Renamed'], json_decode((string) $request->getBody(), true));
    }

    public function testValidationErrorThrows(): void
    {
        $http = new FakeHttpClient();
        $http->queueResponse(new Response(422, [], json_encode([
            'message' => 'The given data was invalid.',
            'errors' => ['name' => ['The name field is required.']],
        ], JSON_THROW_ON_ERROR)));

        $client = $this->client($http);

        try {
            $client->post->newsletter(self::NEWSLETTER_ID)->update(['name' => '']);
            self::fail('Expected ValidationFailedException to be thrown.');
        } catch (ValidationFailedException $e) {
            self::assertSame(422, $e->statusCode);
            self::assertSame(['The name field is required.'], $e->errors['name']);
        }
    }
}
