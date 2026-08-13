import { OpenAPIV3 } from 'openapi-types';
import { PhpFile } from './write.ts';

export interface PhpType {
    hint: string; // native type-hint, ex: "int", "?Website", "int|string"
    docType?: string; // more precise phpdoc type, when `hint` alone loses
                       // information (ex: hint "array", docType "Domain[]")
}

type Schema = OpenAPIV3.SchemaObject | OpenAPIV3.ReferenceObject;

/**
 * Maps a class name (as it appears in a `$ref`, ex: "Website") to the FQCN
 * it's generated at, so cross-references between DTOs resolve to the right
 * import regardless of which Dto/{Base} folder each side lives in.
 */
export type DtoImportMap = Record<string, string>;

export function resolveSchemaType(schema: Schema, file: PhpFile, dtoImports: DtoImportMap): PhpType {
    const nullable = !('$ref' in schema) && schema.nullable === true;
    const base = resolveBaseType(schema, file, dtoImports);
    return applyNullable(base, nullable);
}

function resolveBaseType(schema: Schema, file: PhpFile, dtoImports: DtoImportMap): PhpType {
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

        // PHP rejects a union with a type repeated verbatim (`array|array`,
        // which two differently-shaped oneOf members can both map to, ex.
        // "list of X" vs "map of X" both being native `array`) - so the
        // native hint is deduped, while the phpdoc keeps every member's
        // fuller type for precision.
        const hint = Array.from(new Set(parts.map(part => part.hint))).join('|');
        const docJoined = parts.map(part => part.docType ?? part.hint).join('|');

        return docJoined === hint ? { hint } : { hint, docType: docJoined };
    }

    switch (schema.type) {
        case 'integer':
            return { hint: 'int' };
        case 'number':
            return { hint: 'float' };
        case 'string':
            return { hint: 'string' };
        case 'boolean':
            return { hint: 'bool' };
        case 'array': {
            const items = schema.items as Schema;
            const itemType = resolveSchemaType(items, file, dtoImports);
            const itemDoc = itemType.docType ?? itemType.hint;
            return { hint: 'array', docType: `${itemDoc}[]` };
        }
        case 'object':
        default:
            return { hint: 'array', docType: 'array<string, mixed>' };
    }
}

function resolveRef(ref: string, file: PhpFile, dtoImports: DtoImportMap): PhpType {
    const name = ref.split('/').pop()!;
    const fqcn = dtoImports[name];

    if (!fqcn) {
        throw new Error(`Unknown $ref: ${ref}`);
    }

    return { hint: file.use(fqcn) };
}

function applyNullable(type: PhpType, nullable: boolean): PhpType {
    if (!nullable) {
        return type;
    }

    const docType = type.docType ? `${type.docType}|null` : undefined;

    if (type.hint.includes('|')) {
        return { hint: `${type.hint}|null`, docType };
    }

    return { hint: `?${type.hint}`, docType };
}
