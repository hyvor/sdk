import { pascalCase } from '../helpers.ts';
import { goExported } from './names.ts';
import { CORE_IMPORT, GoFile, GoGeneratedFile } from './write.ts';

/**
 * The product entry-point client (`talk.Client`) is hand-shaped, not
 * schema-derived: it's just the shared `core.Transport` wiring plus an `Org`
 * field and a `{Resource}()` factory for the resource client - both fixed
 * regardless of what the OpenAPI spec's endpoints are.
 */
export function generateProductClientFile(
    product: string,
    resourceName: string,
    orgExample: { group: string, method: string } | null,
    file: GoFile,
): GoGeneratedFile {
    const productPascal = pascalCase(product);
    const resourceGo = goExported(resourceName);
    const resourceClient = `${resourceGo}Client`;
    const idParam = `${resourceName}ID`;
    const core = file.use(CORE_IMPORT);

    const body = [
        `// Package ${product} is the official Go SDK for Hyvor ${productPascal}.`,
        '//',
        '// Org-level access, via a cloud API key:',
        '//',
        `//\tclient, err := ${product}.NewClient(core.WithCloudAPIKey("..."))`,
        ...(orgExample ? [`//\tclient.Org.${goExported(orgExample.group)}.${goExported(orgExample.method)}(ctx, ...)`] : []),
        '//',
        '// Resource-level access, via a per-product API key, no client-level auth needed:',
        '//',
        `//\tclient, err := ${product}.NewClient()`,
        `//\t${resourceName} := client.${resourceGo}(${idParam}, core.WithAPIKey("your-product-api-key"))`,
        '//',
        '// Self-hosted: point directly at your own instance instead of *.hyvor.com:',
        '//',
        `//\tclient, err := ${product}.NewClient(`,
        '//\t\tcore.WithTokenProvider(yourTokenProvider),',
        `//\t\tcore.WithProductURL("https://${product}.example.com"),`,
        '//\t)',
        `package ${product}`,
        '',
        `// Client is the entry point to the Hyvor ${productPascal} SDK.`,
        'type Client struct {',
        `\ttransport *${core}.Transport`,
        '',
        `\t// Org gives org-level access to ${productPascal} resources.`,
        '\tOrg *Org',
        '}',
        '',
        `// NewClient builds a ${productPascal} client. See the ${core}.With... options for the`,
        '// available settings.',
        `func NewClient(opts ...${core}.Option) (*Client, error) {`,
        `\ttransport, err := ${core}.NewTransport(${JSON.stringify(product)}, opts...)`,
        '\tif err != nil {',
        '\t\treturn nil, err',
        '\t}',
        '',
        '\treturn &Client{transport: transport, Org: newOrg(transport)}, nil',
        '}',
        '',
        `// ${resourceGo} gives resource-level access to a single ${resourceName}.`,
        '//',
        `// If no ${core}.WithAPIKey is given, the client's org-level auth is used`,
        `// instead. ${core}.WithDefaultHeaders sets headers merged into every request`,
        '// made through the returned client (and its sub-resources).',
        `func (c *Client) ${resourceGo}(${idParam} int, opts ...${core}.ResourceOption) *${resourceClient} {`,
        `\treturn new${resourceClient}(c.transport, ${idParam}, opts...)`,
        '}',
    ].join('\n');

    return { relativePath: 'client.go', content: file.render(body, true) };
}
