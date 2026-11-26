import { OpenAPIV3 } from 'openapi-types';
import { CORE_IMPORT, GoFile } from './write.ts';

type Schema = OpenAPIV3.SchemaObject | OpenAPIV3.ReferenceObject;

/** schema name (as in a `$ref`) => Go type name */
export type DtoNames = Record<string, string>;

/**
 * Whether the type is generated for a response DTO or a request Input DTO.
 * They differ only in how a free-form JSON object is typed: responses use
 * `core.Map[V]` (tolerates PHP's empty-array-for-empty-object quirk when
 * decoding), requests a plain `map[string]V`.
 */
export type TypeMode = 'response' | 'request';

/** Slices, maps and `any` already have a nil state - never wrapped in a pointer. */
export function isPointerable(type: string): boolean {
    return !(
        type.startsWith('[]')
        || type.startsWith('map[')
        || type.startsWith('core.Map[')
        || type === 'any'
        || type.startsWith('*')
    );
}

export function resolveSchemaType(schema: Schema, file: GoFile, names: DtoNames, mode: TypeMode): string {
    const nullable = !('$ref' in schema) && schema.nullable === true;
    const base = resolveBaseType(schema, file, names, mode);

    return nullable && isPointerable(base) ? `*${base}` : base;
}

function resolveBaseType(schema: Schema, file: GoFile, names: DtoNames, mode: TypeMode): string {
    if ('$ref' in schema) {
        const name = schema.$ref.split('/').pop()!;
        const goName = names[name];
        if (!goName) {
            throw new Error(`Unknown $ref: ${schema.$ref}`);
        }
        return goName;
    }

    // a single-member oneOf/allOf is how the spec expresses "nullable ref"
    // (nullability is handled by the caller): unwrap to the one member.
    for (const union of [schema.oneOf, schema.allOf]) {
        if (union && union.length === 1) {
            return resolveBaseType(union[0], file, names, mode);
        }
    }

    // a real union (ex: "list of strings or a map of strings") has no Go
    // equivalent without custom marshalling: it travels as any.
    if ((schema.oneOf && schema.oneOf.length > 1) || (schema.anyOf && schema.anyOf.length > 0)) {
        return 'any';
    }

    switch (schema.type) {
        case 'integer':
            return 'int';
        case 'number':
            return 'float64';
        case 'string':
            return 'string';
        case 'boolean':
            return 'bool';
        case 'array': {
            const items = schema.items as Schema | undefined;
            return `[]${items ? resolveSchemaType(items, file, names, mode) : 'any'}`;
        }
        case 'object':
        default: {
            if (schema.type === undefined && !schema.properties && !schema.additionalProperties) {
                return 'any';
            }

            const additional = schema.additionalProperties;
            const valueType = additional && typeof additional === 'object'
                ? resolveSchemaType(additional as Schema, file, names, mode)
                : 'any';

            if (mode === 'response') {
                file.use(CORE_IMPORT);
                return `core.Map[${valueType}]`;
            }
            return `map[string]${valueType}`;
        }
    }
}
