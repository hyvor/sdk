import { EndpointsGroup, ProcessedSchema, ProductConfig } from '../schema.ts';
import { renderMethod } from './methodGen.ts';
import { goExported } from './names.ts';
import { DtoNames } from './types.ts';
import { CORE_IMPORT, GoFile, GoGeneratedFile } from './write.ts';

type FileFactory = () => GoFile;

// file names: group tags are snake_case already (ex: "sending_profile")
const fileSlug = (name: string) => name.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '');

function orgResourceName(group: EndpointsGroup): string {
    return `Org${goExported(group.name)}Resource`;
}

function resourceResourceName(resourceName: string, group: EndpointsGroup): string {
    return `${goExported(resourceName)}${goExported(group.name)}Resource`;
}

/**
 * Every package-level identifier the generator itself introduces (besides
 * the DTOs), so a DTO that happens to share one of those names (ex: a Post
 * `Newsletter` DTO is fine, but a DTO called `Client` or `Org` would not
 * be) fails generation loudly instead of producing uncompilable Go.
 */
export function reservedTypeNames(processed: ProcessedSchema): string[] {
    const resourceName = processed.selfGroupName;
    const subGroups = processed.endpoints.resource.filter(group => group.name !== resourceName);

    return [
        'Client',
        'NewClient',
        'Org',
        `${goExported(resourceName)}Client`,
        ...processed.endpoints.org.map(orgResourceName),
        ...subGroups.map(group => resourceResourceName(resourceName, group)),
    ];
}

/**
 * `org.go` (one field per org resource group) plus the org resource group
 * types it wires up (`org_{group}.go`), reachable as `client.Org.{Group}`.
 */
export function generateOrgFiles(
    processed: ProcessedSchema,
    names: DtoNames,
    newFile: FileFactory,
): GoGeneratedFile[] {
    const files: GoGeneratedFile[] = [];
    const groups = processed.endpoints.org;

    const orgFile = newFile();
    const core = orgFile.use(CORE_IMPORT);

    const fields = groups.map(group => `\t${goExported(group.name)} *${orgResourceName(group)}`);
    const assigns = groups.map(group =>
        `\t\t${goExported(group.name)}: &${orgResourceName(group)}{transport: transport},`);

    const orgBody = [
        '// Org gives org-level access to resources, reachable as `client.Org`.',
        '//',
        '// Requires org-level auth (a cloud API key or token provider), since it',
        '// is not scoped to a single resource.',
        'type Org struct {',
        ...fields,
        '}',
        '',
        `func newOrg(transport *${core}.Transport) *Org {`,
        '\treturn &Org{',
        ...assigns,
        '\t}',
        '}',
    ].join('\n');

    files.push({ relativePath: 'org.go', content: orgFile.render(orgBody) });

    for (const group of groups) {
        const file = newFile();
        const fileCore = file.use(CORE_IMPORT);
        const typeName = orgResourceName(group);

        const methods = group.endpoints.map(endpoint =>
            renderMethod(
                endpoint,
                pathExpr => pathExpr,
                { receiver: `r *${typeName}`, request: 'r.transport.Request' },
                file,
                names,
            ),
        );

        const body = [
            `// ${typeName} is reachable as \`client.Org.${goExported(group.name)}\`.`,
            `type ${typeName} struct {`,
            `\ttransport *${fileCore}.Transport`,
            '}',
            '',
            methods.join('\n\n'),
        ].join('\n');

        files.push({ relativePath: `org_${fileSlug(group.name)}.go`, content: file.render(body) });
    }

    return files;
}

/**
 * `{resource}_client.go` (ex: `website_client.go`) - the entry point
 * returned by `client.{Resource}(id)`. It owns `path()`/`request()` (so
 * headers/apiKey don't need to be threaded through every sub-resource),
 * exposes the "self" group's endpoints (ex: `Delete`) directly, and holds
 * one field per remaining resource group, each generated into
 * `{resource}_{group}.go`.
 *
 * How the resource ID actually reaches the API is product-specific (see
 * {@link ProductConfig}): for a 'path'-scoped product it's folded into
 * `path()`'s prefix; for a 'header'-scoped one, `path()` is a no-op and the
 * ID travels as a header merged into every request instead.
 */
export function generateResourceFiles(
    processed: ProcessedSchema,
    names: DtoNames,
    config: ProductConfig,
    newFile: FileFactory,
): GoGeneratedFile[] {
    const files: GoGeneratedFile[] = [];
    const resourceName = processed.selfGroupName;
    const allGroups = processed.endpoints.resource;
    const selfGroup = allGroups.find(group => group.name === resourceName) ?? null;
    const subGroups = allGroups.filter(group => group.name !== resourceName);

    const clientName = `${goExported(resourceName)}Client`;
    const idField = `${resourceName}ID`;
    const resourceSlug = fileSlug(resourceName);

    const selfMethodNames = new Set((selfGroup?.endpoints ?? []).map(endpoint => goExported(endpoint.methodName)));
    for (const group of subGroups) {
        if (selfMethodNames.has(goExported(group.name))) {
            throw new Error(`${clientName}: sub-resource "${group.name}" collides with a method of the same name.`);
        }
    }

    const file = newFile();
    const core = file.use(CORE_IMPORT);
    const context = file.use('context');

    const fields = subGroups.map(group => `\t${goExported(group.name)} *${resourceResourceName(resourceName, group)}`);
    const assigns = subGroups.map(group => `\tc.${goExported(group.name)} = &${resourceResourceName(resourceName, group)}{client: c}`);

    let headersInit: string[];
    let pathBody: string;

    if (config.scope === 'header') {
        const strconv = file.use('strconv');
        headersInit = [
            `\theaders := map[string]string{${JSON.stringify(config.headerName)}: ${strconv}.Itoa(${idField})}`,
            '\tfor name, value := range o.Headers {',
            '\t\theaders[name] = value',
            '\t}',
        ];
        pathBody = '\treturn suffix';
    } else {
        headersInit = ['\theaders := o.Headers'];
        pathBody = `\treturn ${file.use('fmt')}.Sprintf(${JSON.stringify(`${config.pathPrefix}/%d%s`)}, c.${idField}, suffix)`;
    }

    const selfMethods = (selfGroup?.endpoints ?? []).map(endpoint =>
        renderMethod(
            endpoint,
            pathExpr => `c.path(${pathExpr})`,
            { receiver: `c *${clientName}`, request: 'c.request' },
            file,
            names,
        ),
    );

    const body = [
        `// ${clientName} gives resource-level access to a single ${resourceName}, returned by`,
        `// \`client.${goExported(resourceName)}(${idField})\`.`,
        '//',
        "// Authenticated either with the client's org-level auth (a cloud API",
        `// key or token provider, which must have access to this ${resourceName}), or`,
        `// with a resource-level API key (see ${core}.WithAPIKey).`,
        `type ${clientName} struct {`,
        ...fields,
        '',
        '\ttransport *' + `${core}.Transport`,
        `\t${idField} int`,
        '\tapiKey string',
        '\theaders map[string]string',
        '}',
        '',
        `func new${clientName}(transport *${core}.Transport, ${idField} int, opts ...${core}.ResourceOption) *${clientName} {`,
        `\to := ${core}.NewResourceOptions(opts)`,
        ...headersInit,
        '',
        `\tc := &${clientName}{`,
        '\t\ttransport: transport,',
        `\t\t${idField}: ${idField},`,
        '\t\tapiKey: o.APIKey,',
        '\t\theaders: headers,',
        '\t}',
        ...assigns,
        '',
        '\treturn c',
        '}',
        '',
        `func (c *${clientName}) path(suffix string) string {`,
        pathBody,
        '}',
        '',
        `func (c *${clientName}) request(ctx ${context}.Context, method, path string, body any, opts []${core}.RequestOption) ([]byte, error) {`,
        `\treturn c.transport.Do(ctx, method, path, body, ${core}.NewRequestOptions(opts), c.apiKey, c.headers)`,
        '}',
        ...(selfMethods.length ? ['', selfMethods.join('\n\n')] : []),
    ].join('\n');

    files.push({ relativePath: `${resourceSlug}_client.go`, content: file.render(body) });

    for (const group of subGroups) {
        const subFile = newFile();
        const typeName = resourceResourceName(resourceName, group);

        const methods = group.endpoints.map(endpoint =>
            renderMethod(
                endpoint,
                pathExpr => `r.client.path(${pathExpr})`,
                { receiver: `r *${typeName}`, request: 'r.client.request' },
                subFile,
                names,
            ),
        );

        const subBody = [
            `// ${typeName} is reachable as \`client.${goExported(resourceName)}(${idField}).${goExported(group.name)}\`.`,
            `type ${typeName} struct {`,
            `\tclient *${clientName}`,
            '}',
            '',
            methods.join('\n\n'),
        ].join('\n');

        files.push({ relativePath: `${resourceSlug}_${fileSlug(group.name)}.go`, content: subFile.render(subBody) });
    }

    return files;
}
