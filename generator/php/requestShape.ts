import { OpenAPIV3 } from 'openapi-types';

/**
 * Renders a request Input schema's properties as the lines of a phpdoc
 * `array{...}` shape (one `key: type,`/`key?: type,` per line, no
 * indentation - the caller places these inside its own docblock).
 */
export function renderRequestArrayShapeLines(schema: OpenAPIV3.SchemaObject): string[] {
    const required = new Set(schema.required ?? []);

    return Object.entries(schema.properties ?? {}).map(([propName, propSchema]) => {
        const optional = !required.has(propName);
        const type = resolvePropType(propSchema as OpenAPIV3.SchemaObject);
        return `${propName}${optional ? '?' : ''}: ${type},`;
    });
}

function resolvePropType(schema: OpenAPIV3.SchemaObject): string {
    if (schema.enum) {
        const literal = schema.enum.map(value => (typeof value === 'string' ? `'${value}'` : String(value))).join('|');
        return schema.nullable ? `${literal}|null` : literal;
    }

    if (schema.oneOf) {
        const joined = schema.oneOf.map(member => resolvePropType(member as OpenAPIV3.SchemaObject)).join('|');
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
            const itemType = resolvePropType(schema.items as OpenAPIV3.SchemaObject);
            const list = `list<${itemType}>`;
            return schema.nullable ? `${list}|null` : list;
        }
        case 'object':
        default: {
            if (schema.additionalProperties && typeof schema.additionalProperties === 'object') {
                const valueType = resolvePropType(schema.additionalProperties as OpenAPIV3.SchemaObject);
                const map = `array<string, ${valueType}>`;
                return schema.nullable ? `${map}|null` : map;
            }

            return schema.nullable ? 'array<string, mixed>|null' : 'array<string, mixed>';
        }
    }
}
