import { OpenAPIV3 } from 'openapi-types';
import { EndpointsGroup, pascalCase, ProcessedSchema } from '../helpers.ts';
import { PhpFile } from './write.ts';
import { DtoImportMap } from './types.ts';
import { CLIENT_CALLER, renderMethod, SELF_CALLER, TRANSPORT_CALLER } from './methodGen.ts';
import { GeneratedFile } from './dto.ts';

const TRANSPORT_FQCN = 'Hyvor\\Sdk\\Http\\Transport';
const REQUEST_OPTIONS_FQCN = 'Hyvor\\Sdk\\RequestOptions';
const HYVOR_API_EXCEPTION_FQCN = 'Hyvor\\Sdk\\Exceptions\\HyvorApiException';

/**
 * Generates `Org.php` (one property per org resource group) and the org
 * resource group classes it wires up (`Org/{Name}Resource.php`).
 * Accessible via `$client->org->{group}`.
 */
export function generateOrgFiles(
    processed: ProcessedSchema,
    namespaceBase: string,
    dtoImports: DtoImportMap,
): GeneratedFile[] {
    const files: GeneratedFile[] = [];
    const groups = processed.endpoints.org;

    const orgFile = new PhpFile(namespaceBase);
    const transportShort = orgFile.use(TRANSPORT_FQCN);

    const orgProps: string[] = [];
    const orgAssigns: string[] = [];
    for (const group of groups) {
        const classFqcn = `${namespaceBase}\\Org\\${resourceClassName(group)}`;
        const classShort = orgFile.use(classFqcn);
        orgProps.push(`    public readonly ${classShort} $${group.name};`);
        orgAssigns.push(`        $this->${group.name} = new ${classShort}($transport);`);
    }

    const orgBody = [
        '/**',
        ' * Org-level access to resources, accessible via `$client->org`.',
        ' *',
        ' * Requires org-level auth (a cloud API key or token provider), since it',
        ' * is not scoped to a single resource.',
        ' */',
        'final class Org',
        '{',
        ...orgProps,
        '',
        `    public function __construct(${transportShort} $transport)`,
        '    {',
        ...orgAssigns,
        '    }',
        '}',
    ].join('\n');

    files.push({ relativePath: 'Org.php', content: orgFile.render(orgBody) });

    for (const group of groups) {
        files.push(generateOrgResourceFile(group, namespaceBase, dtoImports, processed.requests));
    }

    return files;
}

function generateOrgResourceFile(
    group: EndpointsGroup,
    namespaceBase: string,
    dtoImports: DtoImportMap,
    requestSchemas: Record<string, OpenAPIV3.SchemaObject>,
): GeneratedFile {
    const className = resourceClassName(group);
    const namespace = `${namespaceBase}\\Org`;
    const file = new PhpFile(namespace);
    const transportShort = file.use(TRANSPORT_FQCN);
    file.use(HYVOR_API_EXCEPTION_FQCN);
    file.use(REQUEST_OPTIONS_FQCN);

    const methods = group.endpoints.map(endpoint =>
        renderMethod(endpoint, endpoint.suffix, `'${endpoint.suffix}'`, TRANSPORT_CALLER, file, dtoImports, requestSchemas),
    );

    const body = [
        '/**',
        ` * \`$client->org->${group.name}\``,
        ' */',
        `final class ${className}`,
        '{',
        `    public function __construct(private readonly ${transportShort} $transport)`,
        '    {',
        '    }',
        '',
        methods.join('\n\n'),
        '}',
    ].join('\n');

    return { relativePath: `Org/${className}.php`, content: file.render(body) };
}

/**
 * Generates `{Resource}Client.php` (ex: `WebsiteClient.php`) - the entry
 * point returned by `$client->{resource}($id)`. It owns `path()`/`request()`
 * (so headers/apiKey don't need to be threaded through every sub-resource),
 * exposes the "self" group's endpoints (ex: `delete()`) directly, and holds
 * one property per remaining resource group, each generated into
 * `{Resource}/{Name}Resource.php`.
 */
export function generateWebsiteFiles(
    processed: ProcessedSchema,
    namespaceBase: string,
    dtoImports: DtoImportMap,
): GeneratedFile[] {
    const files: GeneratedFile[] = [];
    const resourceName = processed.selfGroupKey;
    const allGroups = processed.endpoints.resource;
    const selfGroup = allGroups.find(group => group.key === resourceName) ?? null;
    const subGroups = allGroups.filter(group => group.key !== resourceName);

    const clientClassName = `${pascalCase(resourceName)}Client`;
    const clientFqcn = `${namespaceBase}\\${clientClassName}`;
    const idParam = `${resourceName}Id`;

    const clientFile = new PhpFile(namespaceBase);
    const transportShort = clientFile.use(TRANSPORT_FQCN);
    clientFile.use(REQUEST_OPTIONS_FQCN);
    clientFile.use(HYVOR_API_EXCEPTION_FQCN);

    const subProps: string[] = [];
    const subAssigns: string[] = [];
    for (const group of subGroups) {
        const classFqcn = `${namespaceBase}\\${pascalCase(resourceName)}\\${resourceClassName(group)}`;
        const classShort = clientFile.use(classFqcn);
        subProps.push(`    public readonly ${classShort} $${group.name};`);
        subAssigns.push(`        $this->${group.name} = new ${classShort}($this);`);
    }

    const selfMethods = (selfGroup?.endpoints ?? []).map(endpoint =>
        renderMethod(endpoint, endpoint.suffix, `$this->path('${endpoint.suffix}')`, SELF_CALLER, clientFile, dtoImports, processed.requests),
    );

    const clientBody = [
        '/**',
        ` * Resource-level access to a single ${resourceName}, accessible via`,
        ` * \`$client->${resourceName}($${idParam})\`.`,
        ' *',
        ' * Authenticated either with the client\'s org-level auth (a cloud API',
        ` * key or token provider, which must have access to this ${resourceName}), or`,
        ' * with a resource-level API key, passed as `$apiKey`.',
        ' */',
        `final class ${clientClassName}`,
        '{',
        ...subProps,
        (subProps.length ? [''] : []),
        '    /**',
        '     * @param array<string, string> $headers Default headers merged into',
        '     *  every request made through this client and its sub-resources.',
        '     */',
        '    public function __construct(',
        `        public readonly ${transportShort} $transport,`,
        `        private readonly int|string $${idParam},`,
        '        private readonly ?string $apiKey = null,',
        '        private readonly array $headers = [],',
        '    ) {',
        ...subAssigns,
        '    }',
        '',
        '    public function path(string $suffix = \'\'): string',
        '    {',
        `        return "/api/console/v1/{$this->${idParam}}{$suffix}";`,
        '    }',
        '',
        '    /**',
        '     * @param array<mixed>|null $jsonBody',
        '     * @return array<mixed>',
        '     *',
        '     * @throws HyvorApiException',
        '     */',
        '    public function request(',
        '        string $method,',
        '        string $path,',
        '        ?array $jsonBody = null,',
        '        ?RequestOptions $options = null,',
        '    ): array {',
        '        return $this->transport->request($method, $path, $jsonBody, $options, $this->apiKey, $this->headers);',
        '    }',
        ...(selfMethods.length ? ['', selfMethods.join('\n\n')] : []),
        '}',
    ].flat().join('\n');

    files.push({ relativePath: `${clientClassName}.php`, content: clientFile.render(clientBody) });

    for (const group of subGroups) {
        files.push(generateWebsiteResourceFile(group, namespaceBase, resourceName, clientFqcn, idParam, dtoImports, processed.requests));
    }

    return files;
}

function generateWebsiteResourceFile(
    group: EndpointsGroup,
    namespaceBase: string,
    resourceName: string,
    clientFqcn: string,
    idParam: string,
    dtoImports: DtoImportMap,
    requestSchemas: Record<string, OpenAPIV3.SchemaObject>,
): GeneratedFile {
    const className = resourceClassName(group);
    const namespace = `${namespaceBase}\\${pascalCase(resourceName)}`;
    const file = new PhpFile(namespace);
    const clientShort = file.use(clientFqcn);
    file.use(HYVOR_API_EXCEPTION_FQCN);
    file.use(REQUEST_OPTIONS_FQCN);

    const methods = group.endpoints.map(endpoint =>
        renderMethod(endpoint, endpoint.suffix, `$this->client->path('${endpoint.suffix}')`, CLIENT_CALLER, file, dtoImports, requestSchemas),
    );

    const body = [
        '/**',
        ` * \`$client->${resourceName}($${idParam})->${group.name}\``,
        ' */',
        `final class ${className}`,
        '{',
        `    public function __construct(private readonly ${clientShort} $client)`,
        '    {',
        '    }',
        '',
        methods.join('\n\n'),
        '}',
    ].join('\n');

    return { relativePath: `${pascalCase(resourceName)}/${className}.php`, content: file.render(body) };
}

function resourceClassName(group: EndpointsGroup): string {
    return `${pascalCase(group.name)}Resource`;
}
