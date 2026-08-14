import { OpenAPIV3 } from 'openapi-types';
import { TsFile } from './write.ts';

type Schema = OpenAPIV3.SchemaObject | OpenAPIV3.ReferenceObject;

/**
 * Maps a schema name (as it appears in a `$ref`, ex: "WebsiteObject", or a
 * request Input schema, ex: "CreateWebsiteInput") to where its DTO is
 * generated (module path, ex: "Dto") and the name it's exported under (ex:
 * "Website" - the "Object" suffix stripped, see `dtoClassName`), so
 * cross-references between DTOs resolve to the right import.
 */
export type DtoImportMap = Record<string, { modulePath: string, exportName: string }>;

export function resolveSchemaType(schema: Schema, file: TsFile, dtoImports: DtoImportMap): string {
    const nullable = !('$ref' in schema) && schema.nullable === true;
    const base = resolveBaseType(schema, file, dtoImports);
    return nullable ? `${base} | null` : base;
}

function resolveBaseType(schema: Schema, file: TsFile, dtoImports: DtoImportMap): string {
    if ('$ref' in schema) {
        return resolveRef(schema.$ref, file, dtoImports);
    }

    if (schema.oneOf && schema.oneOf.length > 0) {
        // a single-member oneOf is how the spec expresses "nullable ref"
        // (ex: Domain.website); nullability is handled by the caller, so
        // just unwrap to the one member's type here.
        if (schema.oneOf.length === 1) {
            return resolveBaseType(schema.oneOf[0], file, dtoImports);
        }

        const parts = schema.oneOf.map(member => resolveBaseType(member, file, dtoImports));
        return Array.from(new Set(parts)).join(' | ');
    }

    switch (schema.type) {
        case 'integer':
        case 'number':
            return 'number';
        case 'string':
            return 'string';
        case 'boolean':
            return 'boolean';
        case 'array': {
            const items = schema.items as Schema;
            const itemType = resolveSchemaType(items, file, dtoImports);
            return wrapArrayItem(itemType) + '[]';
        }
        case 'object':
        default: {
            if (schema.additionalProperties && typeof schema.additionalProperties === 'object') {
                const valueType = resolveSchemaType(schema.additionalProperties as Schema, file, dtoImports);
                return `Record<string, ${valueType}>`;
            }

            return 'Record<string, unknown>';
        }
    }
}

// a union or nullable item type needs parens before appending `[]`
// (ex: "(Foo | null)[]", not "Foo | null[]")
function wrapArrayItem(type: string): string {
    return type.includes('|') ? `(${type})` : type;
}

function resolveRef(ref: string, file: TsFile, dtoImports: DtoImportMap): string {
    const name = ref.split('/').pop()!;
    const target = dtoImports[name];

    if (!target) {
        throw new Error(`Unknown $ref: ${ref}`);
    }

    return file.use(target, 'type');
}
