import fs from 'fs';
import type { JSONSchema7 } from 'json-schema';
import {OpenAPIV3} from "openapi-types";

export function getSchemas(): Record<string, Record<string, any>> {
    const schemas: Record<string, Record<string, any>> = {};
    const files = fs.readdirSync('../openapi');
    for (const file of files) {
        if (file.endsWith('.json')) {
            const schema = JSON.parse(fs.readFileSync(`../openapi/${file}`, 'utf-8')) as OpenAPIV3.Document;
            schemas[file.replace('.json', '')] = processSchema(schema);
        }
    }
    return schemas;
}

interface ProductConfig {
    resourceName: string; // "website" in Hyvor Talk
}

const PRODUCT_CONFIG = {
    talk: {
        resourceName: 'website'
    }
}

interface ProcessedSchema {
    endpoints: {
        org: EndpointsGroup[],
        resource: EndpointsGroup[]
    },
    requests: Record<string, JSONSchema7>, // CreateWebsiteInput => schema
    responses: Record<string, JSONSchema7> // Website => schema ("Object" suffix is dropped)
}

interface EndpointsGroup {
    name: string, // ex: "domains" (DomainsActions in PHP)
    endpoints: Endpoint[],
}

interface Endpoint {
    method: string,
    path: string,
    requestBody: JSONSchema7 | null,
    response: string | null, // ex: "Website"
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

// the last static (non {param}) path segment is what identifies the resource
// a path's group is keyed off its singular form, so "domains" and "domain" merge
function getGroupKey(path: string): string {
    const staticSegments = path
        .split('/')
        .filter(segment => segment.length > 0 && !segment.startsWith('{'));

    const lastSegment = staticSegments[staticSegments.length - 1] ?? '';

    return singularize(lastSegment.toLowerCase());
}

export function computeEndpointGroups(schema: OpenAPIV3.Document): {
    org: EndpointsGroup[],
    resource: EndpointsGroup[]
} {

    const orgGroups = new Map<string, EndpointsGroup>();
    const resourceGroups = new Map<string, EndpointsGroup>();

    for (const [path, pathItem] of Object.entries(schema.paths ?? {})) {
        if (!pathItem) continue;

        for (const method of HTTP_METHODS) {
            const operation = (pathItem as OpenAPIV3.PathItemObject)[method];
            if (!operation) continue;

            const isOrgEndpoint = (operation.tags ?? []).includes('org-endpoint');
            const groups = isOrgEndpoint ? orgGroups : resourceGroups;

            const key = getGroupKey(path);
            if (!groups.has(key)) {
                groups.set(key, { name: pluralize(key), endpoints: [] });
            }

            groups.get(key)!.endpoints.push({
                method: method.toUpperCase(),
                path,
                requestBody: null,
                response: null,
            });
        }
    }

    return {
        org: Array.from(orgGroups.values()),
        resource: Array.from(resourceGroups.values()),
    };
}

export function processSchema(schema: OpenAPIV3.Document) {

    const { org: orgEndpoints, resource: resourceEndpoints } = computeEndpointGroups(schema);

    console.log('Org Endpoints:', orgEndpoints);
    console.log('Resource Endpoints:', resourceEndpoints);

}