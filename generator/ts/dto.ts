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

const DTO_MODULE_PATH = 'Dto';

/**
 * Maps every response and request Input schema name to where its DTO is
 * generated (`Dto.ts`, one module for the whole product - idiomatic TS
 * keeps related small types together, unlike PHP's one-class-per-file PSR-4
 * layout) and the name it's exported under.
 */
export function buildDtoImportMap(
    responses: Record<string, OpenAPIV3.SchemaObject>,
    requests: Record<string, OpenAPIV3.SchemaObject>,
): DtoImportMap {
    const map: DtoImportMap = {};

    for (const schemaName of Object.keys(responses)) {
        map[schemaName] = { modulePath: DTO_MODULE_PATH, exportName: dtoClassName(schemaName) };
    }

    for (const schemaName of Object.keys(requests)) {
        map[schemaName] = { modulePath: DTO_MODULE_PATH, exportName: dtoClassName(schemaName) };
    }

    return map;
}

function isEnumSchema(schema: OpenAPIV3.SchemaObject): boolean {
    return schema.type === 'string' && Array.isArray(schema.enum);
}

function renderEnum(className: string, schema: OpenAPIV3.SchemaObject): string {
    const cases = (schema.enum as string[]).map(value => `    ${value.toUpperCase()} = '${value}',`);
    return [`export enum ${className} {`, ...cases, '}'].join('\n');
}

// nullability (not optionality) is how a response body expresses "may be
// absent" - matches the PHP SDK's constructor-promoted-property DTOs, where
// every property is a required constructor param.
function renderResponseDto(name: string, schema: OpenAPIV3.SchemaObject, dtoImports: DtoImportMap, file: TsFile): string {
    const className = dtoClassName(name);

    if (isEnumSchema(schema)) {
        return renderEnum(className, schema);
    }

    const propLines = Object.entries(schema.properties ?? {}).map(([propName, propSchema]) => {
        const type = resolveSchemaType(propSchema as OpenAPIV3.SchemaObject, file, dtoImports);
        return `    ${propName}: ${type};`;
    });

    return [`export interface ${className} {`, ...propLines, '}'].join('\n');
}

// a request Input property is marked optional (`?`) when absent from the
// schema's `required` list.
function renderRequestDto(name: string, schema: OpenAPIV3.SchemaObject, dtoImports: DtoImportMap, file: TsFile): string {
    const className = dtoClassName(name);
    const required = new Set(schema.required ?? []);

    const propLines = Object.entries(schema.properties ?? {}).map(([propName, propSchema]) => {
        const type = resolveSchemaType(propSchema as OpenAPIV3.SchemaObject, file, dtoImports);
        const optional = !required.has(propName);
        return `    ${propName}${optional ? '?' : ''}: ${type};`;
    });

    return [`export interface ${className} {`, ...propLines, '}'].join('\n');
}

/**
 * Generates the single `Dto.ts` file: every response schema (as an
 * `interface`, or an `enum` for a string-enum schema) and every request
 * Input schema (as an `interface`), sorted alphabetically by schema name.
 * Cross-references between DTOs (ex: `Mod.user: AuthUser`) need no import -
 * they're all declared in this same module.
 */
export function generateDtoFile(
    responses: Record<string, OpenAPIV3.SchemaObject>,
    requests: Record<string, OpenAPIV3.SchemaObject>,
    dtoImports: DtoImportMap,
): GeneratedFile {
    const file = new TsFile(DTO_MODULE_PATH);

    const sortedEntries = <T>(record: Record<string, T>) =>
        Object.entries(record).sort(([a], [b]) => a.localeCompare(b));

    const blocks = [
        ...sortedEntries(responses).map(([name, schema]) => renderResponseDto(name, schema, dtoImports, file)),
        ...sortedEntries(requests).map(([name, schema]) => renderRequestDto(name, schema, dtoImports, file)),
    ];

    return { relativePath: `${DTO_MODULE_PATH}.ts`, content: file.render(blocks.join('\n\n')) };
}
