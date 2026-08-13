import { pascalCase } from '../helpers.ts';
import { PhpFile } from './write.ts';
import { GeneratedFile } from './dto.ts';

const TOKEN_PROVIDER_FQCN = 'Hyvor\\Sdk\\Auth\\TokenProviderInterface';
const BASE_CLIENT_FQCN = 'Hyvor\\Sdk\\HyvorBaseClientAbstract';
const HTTP_CLIENT_FQCN = 'Psr\\Http\\Client\\ClientInterface';
const REQUEST_FACTORY_FQCN = 'Psr\\Http\\Message\\RequestFactoryInterface';
const STREAM_FACTORY_FQCN = 'Psr\\Http\\Message\\StreamFactoryInterface';
const LOGGER_FQCN = 'Psr\\Log\\LoggerInterface';

/**
 * The product entry-point client (ex: `TalkClient`) is hand-shaped, not
 * schema-derived: it's just the friendly `HyvorBaseClientAbstract` wiring
 * plus an `$org` property and a `{resourceName}()` factory for the resource
 * client - both fixed regardless of what the OpenAPI spec's endpoints are.
 */
export function generateProductClientFile(
    product: string,
    namespaceBase: string,
    resourceName: string,
    orgExampleGroup: string | null,
): GeneratedFile {
    const productPascal = pascalCase(product);
    const resourcePascal = pascalCase(resourceName);
    const clientClassName = `${productPascal}Client`;
    const resourceClientClassName = resourcePascal;

    const file = new PhpFile(namespaceBase);
    file.declareClass(clientClassName);
    const tokenProviderShort = file.use(TOKEN_PROVIDER_FQCN);
    const baseClientShort = file.use(BASE_CLIENT_FQCN);
    const httpClientShort = file.use(HTTP_CLIENT_FQCN);
    const requestFactoryShort = file.use(REQUEST_FACTORY_FQCN);
    const streamFactoryShort = file.use(STREAM_FACTORY_FQCN);
    const loggerShort = file.use(LOGGER_FQCN);
    file.use(`${namespaceBase}\\Org`);
    file.use(`${namespaceBase}\\${resourceClientClassName}`);

    const body = [
        '/**',
        ` * The entry point to the Hyvor ${productPascal} SDK.`,
        ' *',
        ' * ```php',
        ' * // org-level access, via a cloud API key',
        ` * $client = new ${clientClassName}(cloudApiKey: '...');`,
        ...(orgExampleGroup ? [` * $client->org->${orgExampleGroup}->create(...);`] : []),
        ' *',
        ' * // resource-level access, via a per-product API key, no client-level auth needed',
        ` * $client = new ${clientClassName}();`,
        ` * $${resourceName} = $client->${resourceName}($${resourceName}Id, 'your-product-api-key');`,
        ' *',
        ' * // self-hosted: point directly at your own instance instead of *.hyvor.com',
        ` * $client = new ${clientClassName}(tokenProvider: $yourTokenProvider, productUrl: 'https://${product}.example.com');`,
        ' * ```',
        ' *',
        ` * See {@see ${baseClientShort}} for the full constructor parameter docs.`,
        ' */',
        `final class ${clientClassName} extends ${baseClientShort}`,
        '{',
        '    /**',
        `     * Org-level access to ${productPascal} resources, accessible via \`$client->org\`.`,
        '     */',
        '    public readonly Org $org;',
        '',
        '    public function __construct(',
        '        ?string $cloudApiKey = null,',
        `        ?${tokenProviderShort} $tokenProvider = null,`,
        '        ?string $productUrl = null,',
        `        ?${loggerShort} $logger = null,`,
        `        ?${httpClientShort} $httpClient = null,`,
        `        ?${requestFactoryShort} $requestFactory = null,`,
        `        ?${streamFactoryShort} $streamFactory = null,`,
        '        int $retryMaxAttempts = 3,',
        '        float $retryBackoffFactor = 2.0,',
        "        string $cloudInstance = 'https://hyvor.com',",
        '    ) {',
        '        parent::__construct(',
        '            cloudApiKey: $cloudApiKey,',
        '            tokenProvider: $tokenProvider,',
        '            productUrl: $productUrl,',
        '            logger: $logger,',
        '            httpClient: $httpClient,',
        '            requestFactory: $requestFactory,',
        '            streamFactory: $streamFactory,',
        '            retryMaxAttempts: $retryMaxAttempts,',
        '            retryBackoffFactor: $retryBackoffFactor,',
        '            cloudInstance: $cloudInstance,',
        '        );',
        '        $this->org = new Org($this->transport);',
        '    }',
        '',
        '    protected function product(): string',
        '    {',
        `        return '${product}';`,
        '    }',
        '',
        '    /**',
        `     * Resource-level access to a single ${resourceName}.`,
        '     *',
        `     * @param int|string $${resourceName}Id The ${resourceName}'s ID.`,
        '     * @param string|null $apiKey A resource-level API key scoped to this',
        `     *  ${resourceName}. If omitted, the client's org-level auth is used instead.`,
        '     * @param array<string, string> $headers Default headers merged into',
        '     *  every request made through the returned client (and its',
        '     *  sub-resources). Can be overridden per-call via',
        '     *  `RequestOptions::$headers`.',
        '     */',
        `    public function ${resourceName}(int|string $${resourceName}Id, ?string $apiKey = null, array $headers = []): ${resourceClientClassName}`,
        '    {',
        `        return new ${resourceClientClassName}($this->transport, $${resourceName}Id, $apiKey, $headers);`,
        '    }',
        '}',
    ].join('\n');

    return { relativePath: `${clientClassName}.php`, content: file.render(body) };
}
