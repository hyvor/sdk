import fs from 'fs';
import { OpenAPIV3 } from 'openapi-types';

/**
 * How a product's resource-level API scopes requests to one instance of its
 * resource (ex: one website, one newsletter):
 *  - 'path': the ID is a URL path segment (Talk: `/{websiteId}/domains`).
 *  - 'header': the ID travels in a request header instead, and every
 *    resource-scoped path is already complete on its own (Post:
 *    `/newsletter`, with `X-Newsletter-Id` set).
 */
export type ProductConfig =
    | { resourceName: string, scope: 'path', pathPrefix: string }
    | { resourceName: string, scope: 'header', headerName: string };

export const PRODUCT_CONFIG = {
    talk: {
        resourceName: 'website',
        scope: 'path',
        pathPrefix: '/api/console/v1',
    },
    post: {
        resourceName: 'newsletter',
        scope: 'header',
        headerName: 'X-Newsletter-Id',
    },
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
    responses: Record<string, OpenAPIV3.SchemaObject> // WebsiteObject => schema (also holds enum schemas, ex: UserRole)
}

export interface EndpointsGroup {
    name: string, // taken verbatim from the endpoint's `group:{name}` tag
    endpoints: Endpoint[],
}

export interface PathParam {
    name: string, // ex: "id", "email"
    phpType: string, // ex: "int", "string"
}

export interface Endpoint {
    httpMethod: string, // GET, POST, ... - the actual HTTP verb sent on the wire
    methodName: string, // taken verbatim from the `method:{name}` tag - the generated PHP method's name
    path: string, // full path, ex: "/api/console/v1/{websiteId}/domains"
    suffix: string, // path used in generated code: for 'path'-scope products,
                     // relative to the resource ID (ex: "/domains"); for
                     // 'header'-scope products and org endpoints, the full
                     // path. May still contain other path params (ex:
                     // "/issues/{id}/send") - see `pathParams`.
    pathParams: PathParam[], // path params in `suffix` other than the
                              // resource-scoping ID, in order of appearance
    requestSchemaName: string | null, // ex: "CreateWebsiteInput"
    response: {
        shape: 'object' | 'list' | 'raw' | 'void',
        className: string | null, // ex: "WebsiteObject", set only for 'object'/'list'
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

// PHP keywords: illegal as a class/enum name. A stripped DTO name landing on
// one (ex: "ListObject" => "List") keeps its "Object" suffix instead.
const PHP_RESERVED_WORDS = new Set([
    'abstract', 'and', 'array', 'as', 'break', 'callable', 'case', 'catch', 'class', 'clone', 'const', 'continue',
    'declare', 'default', 'die', 'do', 'echo', 'else', 'elseif', 'empty', 'enddeclare', 'endfor', 'endforeach',
    'endif', 'endswitch', 'endwhile', 'enum', 'eval', 'exit', 'extends', 'final', 'finally', 'fn', 'for', 'foreach',
    'function', 'global', 'goto', 'if', 'implements', 'include', 'include_once', 'instanceof', 'insteadof',
    'interface', 'isset', 'list', 'match', 'namespace', 'new', 'or', 'print', 'private', 'protected', 'public',
    'readonly', 'require', 'require_once', 'return', 'static', 'switch', 'throw', 'trait', 'try', 'unset', 'use',
    'var', 'while', 'xor', 'yield', 'int', 'float', 'bool', 'string', 'true', 'false', 'null', 'void', 'iterable',
    'object', 'mixed', 'never', 'self', 'parent',
]);

/**
 * The name a DTO class is generated under: the "Object" suffix (required on
 * every schema an operation returns directly, see {@link resolveResponse})
 * is dropped, ex: `WebsiteObject` => `Website`. Schemas that are only ever
 * embedded in another response, or are enums (ex: `AuthUser`, `UserRole`),
 * never carry the suffix in the first place and pass through unchanged.
 */
export function dtoClassName(schemaName: string): string {
    if (!schemaName.endsWith('Object')) {
        return schemaName;
    }

    const stripped = schemaName.slice(0, -'Object'.length);
    return PHP_RESERVED_WORDS.has(stripped.toLowerCase()) ? schemaName : stripped;
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

// a schema with no properties/items/oneOf and a generic (or no) type carries
// no real information (ex: the `{"type":"object"}` OpenAPI convention for
// "returns an empty body") - treated the same as no schema at all (void)
function isMeaningfulSchema(schema: OpenAPIV3.SchemaObject): boolean {
    if (schema.properties && Object.keys(schema.properties).length > 0) return true;
    if (schema.type === 'array' && schema.items) return true;
    if (schema.oneOf || schema.anyOf || schema.allOf) return true;
    return schema.type !== undefined && schema.type !== 'object';
}

// picks the first 2xx response that carries a JSON schema. A `$ref` (or an
// array of one) is denormalized to that DTO; anything else with real content
// is decoded and handed back as a raw array (we can't derive a precise type
// for an inline/ad-hoc shape); a response with no meaningful schema (ex: an
// empty 204 object) is void.
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

        if (isMeaningfulSchema(schema)) {
            return { shape: 'raw', className: null };
        }
    }

    return { shape: 'void', className: null };
}

function assertObjectSuffix(name: string, path: string, httpMethod: string): void {
    if (!name.endsWith('Object')) {
        throw new Error(`${describe(httpMethod, path)}: response schema "${name}" must end with "Object" (spec bug).`);
    }
}

function mapPathParamScalarType(schema: OpenAPIV3.SchemaObject): string {
    switch (schema.type) {
        case 'integer':
            return 'int';
        case 'number':
            return 'float';
        case 'boolean':
            return 'bool';
        case 'string':
        default:
            return 'string';
    }
}

// OpenAPI documents every path segment as a plain string regardless of its
// real type, so there's no schema signal to type these precisely. "id" is
// numeric everywhere across these APIs, so it gets the friendlier `int`;
// anything else (ex: "email") keeps its own declared scalar type.
function extractPathParams(operation: OpenAPIV3.OperationObject, excludeName: string | null): PathParam[] {
    const params = (operation.parameters ?? []) as OpenAPIV3.ParameterObject[];

    return params
        .filter(param => param.in === 'path' && param.name !== excludeName)
        .map(param => ({
            name: param.name,
            phpType: param.name === 'id' ? 'int' : mapPathParamScalarType((param.schema as OpenAPIV3.SchemaObject) ?? {}),
        }));
}

export function computeEndpointGroups(
    schema: OpenAPIV3.Document,
    config: ProductConfig,
): {
    org: EndpointsGroup[],
    resource: EndpointsGroup[],
} {

    const orgGroups = new Map<string, EndpointsGroup>();
    const resourceGroups = new Map<string, EndpointsGroup>();
    const resourceParamName = config.scope === 'path' ? `${config.resourceName}Id` : null;
    const placeholder = resourceParamName ? `{${resourceParamName}}` : null;

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

            const suffix = isOrgEndpoint || !placeholder
                ? path
                : path.slice(path.indexOf(placeholder) + placeholder.length);

            groups.get(groupName)!.endpoints.push({
                httpMethod: httpMethod.toUpperCase(),
                methodName,
                path,
                suffix,
                pathParams: extractPathParams(operation, resourceParamName),
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
    const endpoints = computeEndpointGroups(schema, config);

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
