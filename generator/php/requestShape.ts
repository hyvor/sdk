import { OpenAPIV3 } from 'openapi-types';
import { refName } from '../schema.ts';

type Schema = OpenAPIV3.SchemaObject | OpenAPIV3.ReferenceObject;

/**
 * Renders a request Input schema's properties as the lines of a phpdoc
 * `array{...}` shape (one `key: type,`/`key?: type,` per line, no
 * indentation - the caller places these inside its own docblock).
 *
 * `schemaRegistry` resolves any `$ref` a property points at (ex: an enum
 * used as a request field, like `role: UserRole`) - array shapes are plain
 * phpdoc, not typed classes, so a ref is inlined as its literal value union
 * rather than referenced by name.
 */
export function renderRequestArrayShapeLines(
    schema: OpenAPIV3.SchemaObject,
    schemaRegistry: Record<string, OpenAPIV3.SchemaObject>,
): string[] {
    const required = new Set(schema.required ?? []);

    return Object.entries(schema.properties ?? {}).map(([propName, propSchema]) => {
        const optional = !required.has(propName);
        const type = resolvePropType(propSchema as Schema, schemaRegistry);
        return `${propName}${optional ? '?' : ''}: ${type},`;
    });
}

function resolvePropType(schema: Schema, schemaRegistry: Record<string, OpenAPIV3.SchemaObject>): string {
    if ('$ref' in schema) {
        const name = refName(schema.$ref);
        const target = schemaRegistry[name];

        if (!target) {
            throw new Error(`Unknown $ref in request schema: "${name}".`);
        }

        if (target.type !== 'string' || !Array.isArray(target.enum)) {
            throw new Error(`Unsupported $ref "${name}" in a request array-shape (expected an enum schema).`);
        }

        return resolvePropType(target, schemaRegistry);
    }

    if (schema.enum) {
        const literal = schema.enum.map(value => (typeof value === 'string' ? `'${value}'` : String(value))).join('|');
        return schema.nullable ? `${literal}|null` : literal;
    }

    if (schema.oneOf) {
        const joined = schema.oneOf.map(member => resolvePropType(member as Schema, schemaRegistry)).join('|');
        return schema.nullable ? `${joined}|null` : joined;
    }

    if (!schema.type) {
        // no type and no enum/oneOf: an unconstrained value, ex: the value
        // schema of `additionalProperties: { nullable: true }`
        return 'mixed';
    }

    switch (schema.type) {
        case 'integer':
            return schema.nullable ? 'int|null' : 'int';
        case 'number':
            return schema.nullable ? 'float|null' : 'float';
        case 'string':
            return schema.nullable ? 'string|null' : 'string';
        case 'boolean':
            return schema.nullable ? 'bool|null' : 'bool';
        case 'array': {
            const itemType = resolvePropType(schema.items as Schema, schemaRegistry);
            const list = `list<${itemType}>`;
            return schema.nullable ? `${list}|null` : list;
        }
        case 'object':
        default: {
            if (schema.additionalProperties && typeof schema.additionalProperties === 'object') {
                const valueType = resolvePropType(schema.additionalProperties as Schema, schemaRegistry);
                const map = `array<string, ${valueType}>`;
                return schema.nullable ? `${map}|null` : map;
            }

            return schema.nullable ? 'array<string, mixed>|null' : 'array<string, mixed>';
        }
    }
}
