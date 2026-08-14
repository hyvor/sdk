import { camelCase, pascalCase } from '../helpers.ts';
import { EndpointsGroup, ProcessedSchema, ProductConfig } from '../schema.ts';
import { TsFile } from './write.ts';
import { DtoImportMap } from './types.ts';
import { CLIENT_CALLER, renderMethod, SELF_CALLER, TRANSPORT_CALLER } from './methodGen.ts';
import { GeneratedFile } from './dto.ts';
import { core } from './core.ts';

// org endpoints address their full path directly - there's no per-instance
// resource client to route the request path through.
const IDENTITY_WRAP = (pathExpr: string) => pathExpr;

function subresourceClassName(group: EndpointsGroup): string {
    return `${pascalCase(group.name)}Resource`;
}

// group tags are snake_case in the OpenAPI specs (ex: "sending_profile");
// exposed as camelCase properties, per TS/JS convention. `reserved` is the
// resource client's own fixed member names (transport, apiKey, headers,
// path, request, the {resource}Id param) - a group whose camelCase name
// collides with one (ex: Post's "api_key" group vs. the fixed `apiKey`
// constructor param) instead uses its full class name, camelCased
// ("apiKeyResource"), which is guaranteed distinct.
function subresourcePropName(group: EndpointsGroup, reserved: ReadonlySet<string> = new Set()): string {
    const camel = camelCase(group.name);
    return reserved.has(camel) ? camelCase(subresourceClassName(group)) : camel;
}

/**
 * Generates `Org.ts` (one property per org resource group) and the org
 * resource group classes it wires up (`Org/{Name}Resource.ts`).
 * Accessible via `client.org.{group}`.
 */
export function generateOrgFiles(
    processed: ProcessedSchema,
    dtoImports: DtoImportMap,
): GeneratedFile[] {
    const files: GeneratedFile[] = [];
    const groups = processed.endpoints.org;

    const orgFile = new TsFile('Org');
    orgFile.declareExport('Org');
    const transportType = orgFile.use(core('Transport'), 'type');

    const orgProps: string[] = [];
    const orgAssigns: string[] = [];
    for (const group of groups) {
        const className = subresourceClassName(group);
        const localName = orgFile.use({ modulePath: `Org/${className}`, exportName: className }, 'value');
        const propName = subresourcePropName(group);
        orgProps.push(`    readonly ${propName}: ${localName};`);
        orgAssigns.push(`        this.${propName} = new ${localName}(transport);`);
    }

    const orgBody = [
        '/**',
        ' * Org-level access to resources, accessible via `client.org`.',
        ' *',
        ' * Requires org-level auth (a cloud API key or token provider), since it',
        ' * is not scoped to a single resource.',
        ' */',
        'export class Org {',
        ...orgProps,
        '',
        `    constructor(transport: ${transportType}) {`,
        ...orgAssigns,
        '    }',
        '}',
    ].join('\n');

    files.push({ relativePath: 'Org.ts', content: orgFile.render(orgBody) });

    for (const group of groups) {
        files.push(generateOrgResourceFile(group, dtoImports));
    }

    return files;
}

function generateOrgResourceFile(group: EndpointsGroup, dtoImports: DtoImportMap): GeneratedFile {
    const className = subresourceClassName(group);
    const modulePath = `Org/${className}`;
    const file = new TsFile(modulePath);
    file.declareExport(className);
    const transportType = file.use(core('Transport'), 'type');

    const methods = group.endpoints.map(endpoint =>
        renderMethod(endpoint, endpoint.suffix, IDENTITY_WRAP, TRANSPORT_CALLER, file, dtoImports),
    );

    const body = [
        '/**',
        ` * \`client.org.${subresourcePropName(group)}\``,
        ' */',
        `export class ${className} {`,
        `    constructor(private readonly transport: ${transportType}) {`,
        '    }',
        '',
        methods.join('\n\n'),
        '}',
    ].join('\n');

    return { relativePath: `${modulePath}.ts`, content: file.render(body) };
}

/**
 * Generates `{Resource}.ts` (ex: `Website.ts`, `Newsletter.ts`) - the entry
 * point returned by `client.{resource}(id)`. It owns `path()`/`request()`
 * (so headers/apiKey don't need to be threaded through every sub-resource),
 * exposes the "self" group's endpoints (ex: `delete()`) directly, and holds
 * one property per remaining resource group, each generated into
 * `{Resource}/{Name}Resource.ts`.
 *
 * How the resource ID actually reaches the API is product-specific (see
 * {@link ProductConfig}): for a 'path'-scoped product it's folded into
 * `path()`'s prefix; for a 'header'-scoped one, `path()` is a no-op and the
 * ID travels as a header merged into every request instead.
 */
export function generateResourceFiles(
    processed: ProcessedSchema,
    dtoImports: DtoImportMap,
    config: ProductConfig,
): GeneratedFile[] {
    const files: GeneratedFile[] = [];
    const resourceName = processed.selfGroupName;
    const allGroups = processed.endpoints.resource;
    const selfGroup = allGroups.find(group => group.name === resourceName) ?? null;
    const subGroups = allGroups.filter(group => group.name !== resourceName);

    const clientClassName = pascalCase(resourceName);
    const idParam = `${resourceName}Id`;

    const clientFile = new TsFile(clientClassName);
    clientFile.declareExport(clientClassName);
    const transportType = clientFile.use(core('Transport'), 'type');
    const requestOptionsType = clientFile.use(core('RequestOptions'), 'type');

    const reservedMembers = new Set(['transport', 'apiKey', 'headers', 'path', 'request', idParam]);
    const subProps: string[] = [];
    const subAssigns: string[] = [];
    const subPropNames = new Map<EndpointsGroup, string>();
    for (const group of subGroups) {
        const subClassName = subresourceClassName(group);
        const localName = clientFile.use({ modulePath: `${clientClassName}/${subClassName}`, exportName: subClassName }, 'value');
        const propName = subresourcePropName(group, reservedMembers);
        subPropNames.set(group, propName);
        subProps.push(`    readonly ${propName}: ${localName};`);
        subAssigns.push(`        this.${propName} = new ${localName}(this);`);
    }

    const selfWrapPath = (pathExpr: string) => `this.path(${pathExpr})`;
    const selfMethods = (selfGroup?.endpoints ?? []).map(endpoint =>
        renderMethod(endpoint, endpoint.suffix, selfWrapPath, SELF_CALLER, clientFile, dtoImports),
    );

    const extraFields: string[] = [];
    const extraCtorLines: string[] = [];
    let pathMethodBody: string;
    let requestHeadersExpr: string;

    if (config.scope === 'header') {
        extraFields.push('    private readonly resourceHeaders: Record<string, string>;');
        extraCtorLines.push(`        this.resourceHeaders = { '${config.headerName}': String(${idParam}), ...headers };`, '');
        pathMethodBody = '        return suffix;';
        requestHeadersExpr = 'this.resourceHeaders';
    } else {
        pathMethodBody = `        return \`${config.pathPrefix}/\${this.${idParam}}\${suffix}\`;`;
        requestHeadersExpr = 'this.headers';
    }

    const clientBody = [
        '/**',
        ` * Resource-level access to a single ${resourceName}, accessible via`,
        ` * \`client.${resourceName}(${idParam})\`.`,
        ' *',
        " * Authenticated either with the client's org-level auth (a cloud API",
        ` * key or token provider, which must have access to this ${resourceName}), or`,
        ' * with a resource-level API key, passed as `apiKey`.',
        ' */',
        `export class ${clientClassName} {`,
        ...subProps,
        ...extraFields,
        '',
        '    constructor(',
        `        readonly transport: ${transportType},`,
        `        private readonly ${idParam}: number | string,`,
        '        private readonly apiKey: string | null = null,',
        '        private readonly headers: Record<string, string> = {},',
        '    ) {',
        ...extraCtorLines,
        ...subAssigns,
        '    }',
        '',
        `    path(suffix: string = ''): string {`,
        pathMethodBody,
        '    }',
        '',
        `    async request(method: string, path: string, jsonBody: unknown = null, options?: ${requestOptionsType}): Promise<unknown> {`,
        `        return this.transport.request(method, path, jsonBody, options, this.apiKey, ${requestHeadersExpr});`,
        '    }',
        ...(selfMethods.length ? ['', selfMethods.join('\n\n')] : []),
        '}',
    ].join('\n');

    files.push({ relativePath: `${clientClassName}.ts`, content: clientFile.render(clientBody) });

    for (const group of subGroups) {
        files.push(generateSubresourceFile(group, subPropNames.get(group)!, clientClassName, idParam, resourceName, dtoImports));
    }

    return files;
}

function generateSubresourceFile(
    group: EndpointsGroup,
    propName: string,
    resourceClassName: string,
    idParam: string,
    resourceName: string,
    dtoImports: DtoImportMap,
): GeneratedFile {
    const className = subresourceClassName(group);
    const modulePath = `${resourceClassName}/${className}`;
    const file = new TsFile(modulePath);
    file.declareExport(className);
    const clientType = file.use({ modulePath: resourceClassName, exportName: resourceClassName }, 'type');

    const wrapPath = (pathExpr: string) => `this.client.path(${pathExpr})`;
    const methods = group.endpoints.map(endpoint =>
        renderMethod(endpoint, endpoint.suffix, wrapPath, CLIENT_CALLER, file, dtoImports),
    );

    const body = [
        '/**',
        ` * \`client.${resourceName}(${idParam}).${propName}\``,
        ' */',
        `export class ${className} {`,
        `    constructor(private readonly client: ${clientType}) {`,
        '    }',
        '',
        methods.join('\n\n'),
        '}',
    ].join('\n');

    return { relativePath: `${modulePath}.ts`, content: file.render(body) };
}
