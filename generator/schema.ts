import fs from 'fs';
import { OpenAPIV3 } from 'openapi-types';

export interface ProductConfig {
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
    // the resource group whose name matches the product's resourceName (ex:
    // "website"): its endpoints attach directly to the resource client
    // (WebsiteClient) instead of becoming a sub-resource class + property.
    selfGroupName: string,
    requests: Record<string, OpenAPIV3.SchemaObject>, // CreateWebsiteInput => schema
    responses: Record<string, OpenAPIV3.SchemaObject> // WebsiteObject => schema
}

export interface EndpointsGroup {
    name: string, // taken verbatim from the endpoint's `group:{name}` tag
    endpoints: Endpoint[],
}

export interface Endpoint {
    httpMethod: string, // GET, POST, ... - the actual HTTP verb sent on the wire
    methodName: string, // taken verbatim from the `method:{name}` tag - the generated PHP method's name
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

const TAG_ORG_ENDPOINT = 'org-endpoint';
const TAG_PREFIX_GROUP = 'group:';
const TAG_PREFIX_METHOD = 'method:';

export function refName(ref: string): string {
    const parts = ref.split('/');
    return parts[parts.length - 1];
}

/**
 * The name a DTO class is generated under: the "Object" suffix (required on
 * every schema an operation returns directly, see {@link resolveResponse})
 * is dropped, ex: `WebsiteObject` => `Website`. Schemas that are only ever
 * embedded in another response (ex: `AuthUser`) never carry the suffix in
 * the first place and pass through unchanged.
 */
export function dtoClassName(schemaName: string): string {
    return schemaName.endsWith('Object') ? schemaName.slice(0, -'Object'.length) : schemaName;
}

function extractTag(tags: string[], prefix: string): string | null {
    const tag = tags.find(t => t.startsWith(prefix));
    return tag ? tag.slice(prefix.length) : null;
}

function describe(httpMethod: string, path: string): string {
    return `${httpMethod.toUpperCase()} ${path}`;
}

function resolveRequestSchemaName(operation: OpenAPIV3.OperationObject, path: string, httpMethod: string): string | null {
    const requestBody = operation.requestBody as OpenAPIV3.RequestBodyObject | undefined;
    const schema = requestBody?.content?.['application/json']?.schema as
        | OpenAPIV3.SchemaObject
        | OpenAPIV3.ReferenceObject
        | undefined;

    if (!schema) {
        return null;
    }

    if (!('$ref' in schema)) {
        throw new Error(`${describe(httpMethod, path)}: request body schema must be a $ref to a named schema.`);
    }

    const name = refName(schema.$ref);
    if (!name.endsWith('Input')) {
        throw new Error(`${describe(httpMethod, path)}: request body schema "${name}" must end with "Input" (spec bug).`);
    }

    return name;
}

// picks the first 2xx response that carries a JSON schema; a response with
// no schema (ex: an empty 204 object) is treated as void
function resolveResponse(operation: OpenAPIV3.OperationObject, path: string, httpMethod: string): Endpoint['response'] {
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
            const name = refName(schema.$ref);
            assertObjectSuffix(name, path, httpMethod);
            return { shape: 'object', className: name };
        }

        if (schema.type === 'array' && schema.items && '$ref' in schema.items) {
            const name = refName(schema.items.$ref);
            assertObjectSuffix(name, path, httpMethod);
            return { shape: 'list', className: name };
        }
    }

    return { shape: 'void', className: null };
}

function assertObjectSuffix(name: string, path: string, httpMethod: string): void {
    if (!name.endsWith('Object')) {
        throw new Error(`${describe(httpMethod, path)}: response schema "${name}" must end with "Object" (spec bug).`);
    }
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

        for (const httpMethod of HTTP_METHODS) {
            const operation = (pathItem as OpenAPIV3.PathItemObject)[httpMethod];
            if (!operation) continue;

            const tags = operation.tags ?? [];
            const isOrgEndpoint = tags.includes(TAG_ORG_ENDPOINT);

            const groupName = extractTag(tags, TAG_PREFIX_GROUP);
            if (!groupName) {
                throw new Error(`${describe(httpMethod, path)}: missing a "group:{name}" tag.`);
            }

            const methodName = extractTag(tags, TAG_PREFIX_METHOD);
            if (!methodName) {
                throw new Error(`${describe(httpMethod, path)}: missing a "method:{name}" tag.`);
            }

            const groups = isOrgEndpoint ? orgGroups : resourceGroups;
            if (!groups.has(groupName)) {
                groups.set(groupName, { name: groupName, endpoints: [] });
            }

            const suffix = isOrgEndpoint
                ? path
                : path.slice(path.indexOf(placeholder) + placeholder.length);

            groups.get(groupName)!.endpoints.push({
                httpMethod: httpMethod.toUpperCase(),
                methodName,
                path,
                suffix,
                requestSchemaName: resolveRequestSchemaName(operation, path, httpMethod),
                response: resolveResponse(operation, path, httpMethod),
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
        selfGroupName: config.resourceName,
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
