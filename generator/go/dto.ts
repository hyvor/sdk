import { OpenAPIV3 } from 'openapi-types';
import { dtoClassName } from '../schema.ts';
import { docLines, goExported } from './names.ts';
import { DtoNames, isPointerable, resolveSchemaType, TypeMode } from './types.ts';
import { GoFile, GoGeneratedFile } from './write.ts';

/**
 * Maps every response and request Input schema name to its Go type name:
 * the "Object" suffix is dropped from response schemas (`WebsiteObject` =>
 * `Website`), exactly as the PHP and TS generators do.
 */
export function buildDtoNames(
    responses: Record<string, OpenAPIV3.SchemaObject>,
    requests: Record<string, OpenAPIV3.SchemaObject>,
): DtoNames {
    const names: DtoNames = {};

    for (const schemaName of [...Object.keys(responses), ...Object.keys(requests)]) {
        const name = dtoClassName(schemaName);
        names[schemaName] = name.charAt(0).toUpperCase() + name.slice(1);
    }

    return names;
}

function isEnumSchema(schema: OpenAPIV3.SchemaObject): boolean {
    return schema.type === 'string' && Array.isArray(schema.enum);
}

function renderEnum(typeName: string, schema: OpenAPIV3.SchemaObject): string {
    const lines: string[] = [];

    if (schema.description) {
        lines.push(...docLines(`${typeName}: ${schema.description}`));
    }
    lines.push(`type ${typeName} string`, '', 'const (');

    const seen = new Set<string>();
    for (const value of schema.enum as string[]) {
        const constName = `${typeName}${goExported(value)}`;
        if (seen.has(constName)) {
            throw new Error(`Enum ${typeName}: two values map to the same Go constant "${constName}".`);
        }
        seen.add(constName);
        lines.push(`\t${constName} ${typeName} = ${JSON.stringify(value)}`);
    }
    lines.push(')');

    return lines.join('\n');
}

function renderStruct(
    typeName: string,
    schema: OpenAPIV3.SchemaObject,
    names: DtoNames,
    file: GoFile,
    mode: TypeMode,
): string {
    const required = new Set(schema.required ?? []);
    const usedFieldNames = new Set<string>();
    const lines: string[] = [];

    if (schema.description) {
        lines.push(...docLines(`${typeName}: ${schema.description}`));
    }
    lines.push(`type ${typeName} struct {`);

    for (const [propName, propSchema] of Object.entries(schema.properties ?? {})) {
        let fieldName = goExported(propName);
        while (usedFieldNames.has(fieldName)) {
            fieldName += '_';
        }
        usedFieldNames.add(fieldName);

        let type = resolveSchemaType(propSchema, file, names, mode);
        let tag = propName;

        // a response body expresses "may be absent" with nullability, which
        // already made the type a pointer where Go needs one. A request
        // Input property absent from `required` means "omit this field":
        // pointers (so a zero value like false/0 can still be sent) with
        // omitempty, or omitzero for slices/maps/any, so an explicitly empty
        // (non-nil) list still gets sent.
        if (mode === 'request' && !required.has(propName)) {
            if (isPointerable(type)) {
                type = `*${type}`;
                tag += ',omitempty';
            } else if (type.startsWith('*') || type === 'any') {
                tag += ',omitempty';
            } else {
                tag += ',omitzero';
            }
        }

        const description = !('$ref' in propSchema) ? propSchema.description : undefined;
        if (description) {
            lines.push(...docLines(description, '\t'));
        }
        lines.push(`\t${fieldName} ${type} \`json:"${tag}"\``);
    }

    lines.push('}');
    return lines.join('\n');
}

/**
 * Generates `dto.go`: every response schema (as a `struct`, or a string type
 * plus constants for a string-enum schema) and every request Input schema,
 * sorted by name.
 */
export function generateDtoFile(
    responses: Record<string, OpenAPIV3.SchemaObject>,
    requests: Record<string, OpenAPIV3.SchemaObject>,
    names: DtoNames,
    file: GoFile,
): GoGeneratedFile {
    const blocks: { name: string, code: string }[] = [];

    for (const [schemaName, schema] of Object.entries(responses)) {
        const typeName = names[schemaName];
        blocks.push({
            name: typeName,
            code: isEnumSchema(schema) ? renderEnum(typeName, schema) : renderStruct(typeName, schema, names, file, 'response'),
        });
    }

    for (const [schemaName, schema] of Object.entries(requests)) {
        const typeName = names[schemaName];
        blocks.push({
            name: typeName,
            code: isEnumSchema(schema) ? renderEnum(typeName, schema) : renderStruct(typeName, schema, names, file, 'request'),
        });
    }

    blocks.sort((a, b) => a.name.localeCompare(b.name));

    return { relativePath: 'dto.go', content: file.render(blocks.map(block => block.code).join('\n\n')) };
}
