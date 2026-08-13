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

export function processSchema(schema: OpenAPIV3.Document) {

    const orgEndpoints: EndpointsGroup[] = [];
    const resourceEndpoints: EndpointsGroup[] = [];



}