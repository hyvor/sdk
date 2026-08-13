import { OpenAPIV3 } from 'openapi-types';
import { dtoClassName } from '../schema.ts';
import { PhpFile } from './write.ts';
import { DtoImportMap, resolveSchemaType } from './types.ts';

export interface GeneratedFile {
    relativePath: string;
    content: string;
}

export function buildDtoImportMap(responses: Record<string, OpenAPIV3.SchemaObject>, namespaceBase: string): DtoImportMap {
    const map: DtoImportMap = {};

    for (const schemaName of Object.keys(responses)) {
        map[schemaName] = `${namespaceBase}\\Dto\\${dtoClassName(schemaName)}`;
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
    schemaName: string,
    schema: OpenAPIV3.SchemaObject,
    dtoImports: DtoImportMap,
    namespaceBase: string,
): GeneratedFile {
    const className = dtoClassName(schemaName);
    const namespace = `${namespaceBase}\\Dto`;
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
        `final class ${className}`,
        '{',
        '    public function __construct(',
        ...paramLines,
        '    ) {',
        '    }',
        '}',
    ].join('\n');

    return {
        relativePath: `Dto/${className}.php`,
        content: file.render(body),
    };
}
