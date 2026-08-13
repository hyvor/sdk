import fs from 'fs';
import { OpenAPIV3 } from 'openapi-types';

interface ProductConfig {
    resourceName: string; // "website" in Hyvor Talk - the entity a ResourceClient scopes to
}

export const PRODUCT_CONFIG = {
    talk: {
        resourceName: 'website'
    }
} satisfies Record<string, ProductConfig>;

export type Product = keyof typeof PRODUCT_CONFIG;

export interface ProcessedSchema {
    endpoints: {
        org: EndpointsGroup[],
        resource: EndpointsGroup[]
    },
    // the resource group whose key matches the product's resourceName (e.g.
    // "website"): its endpoints attach directly to the resource client
    // (WebsiteClient) instead of becoming a sub-resource class + property.
    selfGroupKey: string,
    requests: Record<string, OpenAPIV3.SchemaObject>, // CreateWebsiteInput => schema
    responses: Record<string, OpenAPIV3.SchemaObject> // WebsiteObject => schema
}

export interface EndpointsGroup {
    key: string, // singular, ex: "domain" - used to detect the self group
    name: string, // plural, ex: "domains" (DomainsResource, $domains)
    endpoints: Endpoint[],
}

export interface Endpoint {
    method: string, // GET, POST, ...
    path: string, // full path, ex: "/api/console/v1/{websiteId}/domains"
    suffix: string, // path used in generated code: relative for resource
                     // groups (ex: "/domains"), full path for org groups
    requestSchemaName: string | null, // ex: "CreateWebsiteInput"
    response: {
        shape: 'object' | 'list' | 'void',
        className: string | null, // ex: "WebsiteObject"
    },
}

const HTTP_METHODS = ['get', 'put', 'post', 'delete', 'patch'] as const;

// naive english singularization, ordered most-specific rule first
const SINGULAR_RULES: [RegExp, string][] = [
    [/ies$/i, 'y'],           // categories => category
    [/(x|ch|sh|us|ss)es$/i, '$1'], // boxes => box, statuses => status, classes => class
    [/([^s])s$/i, '$1'],      // domains => domain, mods => mod ("website" is untouched)
];

function singularize(word: string): string {
    for (const [pattern, replacement] of SINGULAR_RULES) {
        if (pattern.test(word)) {
            return word.replace(pattern, replacement);
        }
    }
    return word;
}

function pluralize(word: string): string {
    if (/[^aeiou]y$/i.test(word)) {
        return word.slice(0, -1) + 'ies';
    }
    if (/(s|x|z|ch|sh)$/i.test(word)) {
        return word + 'es';
    }
    return word + 's';
}

export function pascalCase(word: string): string {
    return word
        .split(/[^a-zA-Z0-9]+/)
        .filter(Boolean)
        .map(part => part.charAt(0).toUpperCase() + part.slice(1))
        .join('');
}

export function refName(ref: string): string {
    const parts = ref.split('/');
    return parts[parts.length - 1];
}

// the last static (non {param}) path segment is what identifies the resource
// a path's group is keyed off its singular form, so "domains" and "domain" merge
function getGroupKey(path: string): string {
    const staticSegments = path
        .split('/')
        .filter(segment => segment.length > 0 && !segment.startsWith('{'));

    const lastSegment = staticSegments[staticSegments.length - 1] ?? '';

    return singularize(lastSegment.toLowerCase());
}

function resolveRequestSchemaName(operation: OpenAPIV3.OperationObject): string | null {
    const requestBody = operation.requestBody as OpenAPIV3.RequestBodyObject | undefined;
    const schema = requestBody?.content?.['application/json']?.schema as
        | OpenAPIV3.ReferenceObject
        | undefined;

    if (schema && '$ref' in schema) {
        return refName(schema.$ref);
    }

    return null;
}

// picks the first 2xx response that carries a JSON schema; a response with
// no schema (ex: an empty 204 object) is treated as void
function resolveResponse(operation: OpenAPIV3.OperationObject): Endpoint['response'] {
    const responses = operation.responses ?? {};
    const codes = Object.keys(responses).sort();

    for (const code of codes) {
        const responseObject = responses[code] as OpenAPIV3.ResponseObject;
        const schema = responseObject.content?.['application/json']?.schema as
            | OpenAPIV3.SchemaObject
            | OpenAPIV3.ReferenceObject
            | undefined;

        if (!schema) continue;

        if ('$ref' in schema) {
            return { shape: 'object', className: refName(schema.$ref) };
        }

        if (schema.type === 'array' && schema.items && '$ref' in schema.items) {
            return { shape: 'list', className: refName(schema.items.$ref) };
        }
    }

    return { shape: 'void', className: null };
}

export function computeEndpointGroups(
    schema: OpenAPIV3.Document,
    resourceParamName: string,
): {
    org: EndpointsGroup[],
    resource: EndpointsGroup[],
} {

    const orgGroups = new Map<string, EndpointsGroup>();
    const resourceGroups = new Map<string, EndpointsGroup>();
    const placeholder = `{${resourceParamName}}`;

    for (const [path, pathItem] of Object.entries(schema.paths ?? {})) {
        if (!pathItem) continue;

        for (const method of HTTP_METHODS) {
            const operation = (pathItem as OpenAPIV3.PathItemObject)[method];
            if (!operation) continue;

            const isOrgEndpoint = (operation.tags ?? []).includes('org-endpoint');
            const groups = isOrgEndpoint ? orgGroups : resourceGroups;

            const key = getGroupKey(path);
            if (!groups.has(key)) {
                groups.set(key, { key, name: pluralize(key), endpoints: [] });
            }

            const suffix = isOrgEndpoint
                ? path
                : path.slice(path.indexOf(placeholder) + placeholder.length);

            groups.get(key)!.endpoints.push({
                method: method.toUpperCase(),
                path,
                suffix,
                requestSchemaName: resolveRequestSchemaName(operation),
                response: resolveResponse(operation),
            });
        }
    }

    return {
        org: Array.from(orgGroups.values()),
        resource: Array.from(resourceGroups.values()),
    };
}

export function processSchema(schema: OpenAPIV3.Document, product: Product): ProcessedSchema {
    const config = PRODUCT_CONFIG[product];
    const resourceParamName = `${config.resourceName}Id`;
    const endpoints = computeEndpointGroups(schema, resourceParamName);

    const requests: Record<string, OpenAPIV3.SchemaObject> = {};
    const responses: Record<string, OpenAPIV3.SchemaObject> = {};

    for (const [name, componentSchema] of Object.entries(schema.components?.schemas ?? {})) {
        if ('$ref' in componentSchema) continue;

        if (name.endsWith('Input')) {
            requests[name] = componentSchema;
        } else {
            responses[name] = componentSchema;
        }
    }

    return {
        endpoints,
        selfGroupKey: config.resourceName,
        requests,
        responses,
    };
}

export function getSchemas(): Record<Product, ProcessedSchema> {
    const schemas = {} as Record<Product, ProcessedSchema>;
    const files = fs.readdirSync('../openapi');

    for (const file of files) {
        if (!file.endsWith('.json')) continue;

        const product = file.replace('.json', '') as Product;
        const schema = JSON.parse(fs.readFileSync(`../openapi/${file}`, 'utf-8')) as OpenAPIV3.Document;
        schemas[product] = processSchema(schema, product);
    }

    return schemas;
}
