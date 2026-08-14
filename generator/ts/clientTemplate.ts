import { pascalCase } from '../helpers.ts';
import { TsFile } from './write.ts';
import { GeneratedFile } from './dto.ts';
import { core } from './core.ts';

/**
 * The product entry-point client (ex: `TalkClient`) is hand-shaped, not
 * schema-derived: it's just the friendly `HyvorBaseClient` wiring plus an
 * `org` property and a `{resourceName}()` factory for the resource client -
 * both fixed regardless of what the OpenAPI spec's endpoints are.
 */
export function generateProductClientFile(
    product: string,
    resourceName: string,
    orgExampleGroup: string | null,
): GeneratedFile {
    const productPascal = pascalCase(product);
    const resourcePascal = pascalCase(resourceName);
    const clientClassName = `${productPascal}Client`;
    const idParam = `${resourceName}Id`;

    const file = new TsFile(clientClassName);
    file.declareExport(clientClassName);

    const baseClientType = file.use(core('HyvorBaseClient'), 'value');
    const baseClientOptionsType = file.use(core('HyvorBaseClientOptions'), 'type');
    const orgType = file.use({ modulePath: 'Org', exportName: 'Org' }, 'value');
    const resourceType = file.use({ modulePath: resourcePascal, exportName: resourcePascal }, 'value');

    const body = [
        '/**',
        ` * The entry point to the Hyvor ${productPascal} SDK.`,
        ' *',
        ' * ```ts',
        ' * // org-level access, via a cloud API key',
        ` * const client = new ${clientClassName}({ cloudApiKey: '...' });`,
        ...(orgExampleGroup ? [` * client.org.${orgExampleGroup}.create(...);`] : []),
        ' *',
        ' * // resource-level access, via a per-product API key, no client-level auth needed',
        ` * const client = new ${clientClassName}();`,
        ` * const ${resourceName} = client.${resourceName}(${idParam}, 'your-product-api-key');`,
        ' *',
        ' * // self-hosted: point directly at your own instance instead of *.hyvor.com',
        ` * const client = new ${clientClassName}({ tokenProvider: yourTokenProvider, productUrl: 'https://${product}.example.com' });`,
        ' * ```',
        ' *',
        ` * See {@link ${baseClientType}} for the full constructor option docs.`,
        ' */',
        `export class ${clientClassName} extends ${baseClientType} {`,
        '    /**',
        `     * Org-level access to ${productPascal} resources, accessible via \`client.org\`.`,
        '     */',
        `    readonly org: ${orgType};`,
        '',
        `    constructor(options: ${baseClientOptionsType} = {}) {`,
        `        super('${product}', options);`,
        '        this.org = new Org(this.transport);',
        '    }',
        '',
        '    /**',
        `     * Resource-level access to a single ${resourceName}.`,
        '     *',
        `     * @param ${idParam} The ${resourceName}'s ID.`,
        `     * @param apiKey A resource-level API key scoped to this ${resourceName}. If`,
        "     *  omitted, the client's org-level auth is used instead.",
        '     * @param headers Default headers merged into every request made through',
        '     *  the returned client (and its sub-resources). Can be overridden',
        '     *  per-call via `RequestOptions.headers`.',
        '     */',
        `    ${resourceName}(${idParam}: number | string, apiKey: string | null = null, headers: Record<string, string> = {}): ${resourceType} {`,
        `        return new ${resourceType}(this.transport, ${idParam}, apiKey, headers);`,
        '    }',
        '}',
    ].join('\n');

    return { relativePath: `${clientClassName}.ts`, content: file.render(body) };
}
