import { OpenAPIV3 } from 'openapi-types';
import { PhpFile } from './write.ts';
import { DtoImportMap, resolveSchemaType } from './types.ts';

export interface GeneratedFile {
    relativePath: string;
    content: string;
}

// pairs like Website/WebsiteObject or Domain/DomainObject share a folder,
// keyed off the name with any "Object" suffix stripped
function dtoBase(name: string): string {
    return name.endsWith('Object') ? name.slice(0, -'Object'.length) : name;
}

export function buildDtoImportMap(responses: Record<string, OpenAPIV3.SchemaObject>, namespaceBase: string): DtoImportMap {
    const map: DtoImportMap = {};

    for (const name of Object.keys(responses)) {
        map[name] = `${namespaceBase}\\Dto\\${dtoBase(name)}\\${name}`;
    }

    return map;
}

export function generateDtoFiles(
    responses: Record<string, OpenAPIV3.SchemaObject>,
    dtoImports: DtoImportMap,
    namespaceBase: string,
): GeneratedFile[] {
    return Object.entries(responses).map(([name, schema]) => generateDtoFile(name, schema, dtoImports, namespaceBase));
}

function generateDtoFile(
    name: string,
    schema: OpenAPIV3.SchemaObject,
    dtoImports: DtoImportMap,
    namespaceBase: string,
): GeneratedFile {
    const base = dtoBase(name);
    const namespace = `${namespaceBase}\\Dto\\${base}`;
    const file = new PhpFile(namespace);

    const paramLines: string[] = [];
    for (const [propName, propSchema] of Object.entries(schema.properties ?? {})) {
        const type = resolveSchemaType(propSchema as OpenAPIV3.SchemaObject, file, dtoImports);

        if (type.docType) {
            paramLines.push(`        /** @var ${type.docType} */`);
        }
        paramLines.push(`        public readonly ${type.hint} $${propName},`);
    }

    const body = [
        `final class ${name}`,
        '{',
        '    public function __construct(',
        ...paramLines,
        '    ) {',
        '    }',
        '}',
    ].join('\n');

    return {
        relativePath: `Dto/${base}/${name}.php`,
        content: file.render(body),
    };
}
