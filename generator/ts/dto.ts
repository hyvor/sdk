import { OpenAPIV3 } from 'openapi-types';
import { TsFile } from './write.ts';
import { DtoImportMap, resolveSchemaType } from './types.ts';

export interface GeneratedFile {
    relativePath: string;
    content: string;
}

// JS/TS reserved words illegal as a type/interface/enum identifier. A
// stripped DTO name landing on one keeps its "Object" suffix instead (guards
// against a hypothetical schema, ex: "DeleteObject" - none of today's
// schemas trigger this).
const TS_RESERVED_WORDS = new Set([
    'break', 'case', 'catch', 'class', 'const', 'continue', 'debugger', 'default', 'delete', 'do',
    'else', 'enum', 'export', 'extends', 'false', 'finally', 'for', 'function', 'if', 'import', 'in',
    'instanceof', 'new', 'null', 'return', 'super', 'switch', 'this', 'throw', 'true', 'try', 'typeof',
    'var', 'void', 'while', 'with', 'implements', 'interface', 'let', 'package', 'private', 'protected',
    'public', 'static', 'yield', 'await',
]);

/**
 * The name a response DTO is generated under: the "Object" suffix (required
 * on every schema an operation returns directly) is dropped, ex:
 * `WebsiteObject` => `Website`. Schemas that are only ever embedded in
 * another response, or are enums (ex: `AuthUser`, `UserRole`), never carry
 * the suffix in the first place and pass through unchanged - as do request
 * Input schemas (ex: `CreateWebsiteInput`), which never carry it either.
 */
export function dtoClassName(schemaName: string): string {
    if (!schemaName.endsWith('Object')) {
        return schemaName;
    }

    const stripped = schemaName.slice(0, -'Object'.length);
    return TS_RESERVED_WORDS.has(stripped.toLowerCase()) ? schemaName : stripped;
}

/**
 * Maps every response and request Input schema name to the module path its
 * DTO/interface is generated at, so `resolveSchemaType` can resolve a `$ref`
 * regardless of whether it points at a response object, an embedded enum, or
 * a request Input schema - all three live side by side under `Dto/`.
 */
export function buildDtoImportMap(
    responses: Record<string, OpenAPIV3.SchemaObject>,
    requests: Record<string, OpenAPIV3.SchemaObject>,
): DtoImportMap {
    const map: DtoImportMap = {};

    for (const schemaName of Object.keys(responses)) {
        map[schemaName] = `Dto/${dtoClassName(schemaName)}`;
    }

    for (const schemaName of Object.keys(requests)) {
        map[schemaName] = `Dto/${dtoClassName(schemaName)}`;
    }

    return map;
}

function isEnumSchema(schema: OpenAPIV3.SchemaObject): boolean {
    return schema.type === 'string' && Array.isArray(schema.enum);
}

/**
 * Generates one file per response schema (ex: `Dto/Website.ts`): an
 * `interface` with every property required (nullability, not optionality,
 * is how the API expresses "may be absent" in a response body - matches the
 * PHP SDK's constructor-promoted-property DTOs), or an `enum` for a
 * string-enum schema.
 */
export function generateResponseDtoFiles(
    responses: Record<string, OpenAPIV3.SchemaObject>,
    dtoImports: DtoImportMap,
): GeneratedFile[] {
    return Object.entries(responses).map(([name, schema]) => {
        const className = dtoClassName(name);
        const modulePath = `Dto/${className}`;
        const file = new TsFile(modulePath);
        file.declareExport(className);

        if (isEnumSchema(schema)) {
            return generateEnumFile(className, schema, file, modulePath);
        }

        const propLines = Object.entries(schema.properties ?? {}).map(([propName, propSchema]) => {
            const type = resolveSchemaType(propSchema as OpenAPIV3.SchemaObject, file, dtoImports);
            return `    ${propName}: ${type};`;
        });

        const body = [`export interface ${className} {`, ...propLines, '}'].join('\n');

        return { relativePath: `${modulePath}.ts`, content: file.render(body) };
    });
}

/**
 * Generates one file per request Input schema (ex: `Dto/CreateWebsiteInput.ts`):
 * an `interface` with a property marked optional (`?`) when absent from the
 * schema's `required` list.
 */
export function generateRequestDtoFiles(
    requests: Record<string, OpenAPIV3.SchemaObject>,
    dtoImports: DtoImportMap,
): GeneratedFile[] {
    return Object.entries(requests).map(([name, schema]) => {
        const className = dtoClassName(name);
        const modulePath = `Dto/${className}`;
        const file = new TsFile(modulePath);
        file.declareExport(className);

        const required = new Set(schema.required ?? []);
        const propLines = Object.entries(schema.properties ?? {}).map(([propName, propSchema]) => {
            const type = resolveSchemaType(propSchema as OpenAPIV3.SchemaObject, file, dtoImports);
            const optional = !required.has(propName);
            return `    ${propName}${optional ? '?' : ''}: ${type};`;
        });

        const body = [`export interface ${className} {`, ...propLines, '}'].join('\n');

        return { relativePath: `${modulePath}.ts`, content: file.render(body) };
    });
}

function generateEnumFile(className: string, schema: OpenAPIV3.SchemaObject, file: TsFile, modulePath: string): GeneratedFile {
    const cases = (schema.enum as string[]).map(value => `    ${value.toUpperCase()} = '${value}',`);
    const body = [`export enum ${className} {`, ...cases, '}'].join('\n');

    return { relativePath: `${modulePath}.ts`, content: file.render(body) };
}
