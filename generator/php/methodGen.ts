import { OpenAPIV3 } from 'openapi-types';
import { Endpoint, PathParam } from '../schema.ts';
import { PhpFile } from './write.ts';
import { DtoImportMap } from './types.ts';
import { renderRequestArrayShapeLines } from './requestShape.ts';

/**
 * Where a method sends its request through and denormalizes its response
 * through - both are `$this->transport` for org resources and methods
 * declared directly on the resource client (WebsiteClient), but
 * `$this->client`/`$this->client->transport` for a sub-resource that only
 * holds a reference to its owning resource client.
 */
export interface CallerContext {
    requestReceiver: string;
    transportReceiver: string;
}

export const TRANSPORT_CALLER: CallerContext = {
    requestReceiver: '$this->transport',
    transportReceiver: '$this->transport',
};

export const CLIENT_CALLER: CallerContext = {
    requestReceiver: '$this->client',
    transportReceiver: '$this->client->transport',
};

// for methods declared directly on the resource client (ex: WebsiteClient's
// own `delete()`): requests must still go through its own `request()` so
// apiKey/headers get applied, but denormalization can talk to the transport
// directly.
export const SELF_CALLER: CallerContext = {
    requestReceiver: '$this',
    transportReceiver: '$this->transport',
};

/**
 * Builds the PHP expression for an endpoint's request path: a plain quoted
 * literal when it has no path params, or a `.`-concatenation interpolating
 * each one otherwise (ex: `'/issues/' . $id . '/send'`). Non-`int` params
 * (ex: an email) are `rawurlencode()`-wrapped, since they can't be trusted
 * to be URL-safe as-is.
 */
function buildPathExpr(suffix: string, pathParams: PathParam[]): string {
    if (pathParams.length === 0) {
        return `'${suffix}'`;
    }

    const paramsByName = new Map(pathParams.map(param => [param.name, param]));
    const parts: string[] = [];
    const placeholderPattern = /\{(\w+)\}/g;
    let lastIndex = 0;
    let match: RegExpExecArray | null;

    while ((match = placeholderPattern.exec(suffix)) !== null) {
        const literal = suffix.slice(lastIndex, match.index);
        if (literal) parts.push(`'${literal}'`);

        const param = paramsByName.get(match[1]);
        if (!param) {
            throw new Error(`Path param "{${match[1]}}" in "${suffix}" has no matching parameter.`);
        }

        parts.push(param.phpType === 'int' ? `$${param.name}` : `rawurlencode($${param.name})`);
        lastIndex = placeholderPattern.lastIndex;
    }

    const trailing = suffix.slice(lastIndex);
    if (trailing) parts.push(`'${trailing}'`);

    return parts.join(' . ');
}

export function renderMethod(
    endpoint: Endpoint,
    docPath: string,
    wrapPath: (pathExpr: string) => string,
    caller: CallerContext,
    file: PhpFile,
    dtoImports: DtoImportMap,
    requestSchemas: Record<string, OpenAPIV3.SchemaObject>,
    schemaRegistry: Record<string, OpenAPIV3.SchemaObject>,
): string {
    const name = endpoint.methodName;
    const hasData = endpoint.requestSchemaName !== null;
    const dataExpr = hasData ? '$data' : 'null';

    const params = [
        ...endpoint.pathParams.map(param => `${param.phpType} $${param.name}`),
        ...(hasData ? ['array $data'] : []),
        '?RequestOptions $options = null',
    ].join(', ');

    let returnType = 'void';
    let returnDoc: string | null = null;

    if (endpoint.response.shape === 'object' && endpoint.response.className) {
        returnType = file.use(dtoImports[endpoint.response.className]);
    } else if (endpoint.response.shape === 'list' && endpoint.response.className) {
        const itemType = file.use(dtoImports[endpoint.response.className]);
        returnType = 'array';
        returnDoc = `${itemType}[]`;
    } else if (endpoint.response.shape === 'raw') {
        returnType = 'array';
        returnDoc = 'array<mixed>';
    }

    const docLines = [`     * ${endpoint.httpMethod} ${docPath}`];

    if (hasData) {
        const schema = requestSchemas[endpoint.requestSchemaName!];
        docLines.push('     *');
        docLines.push('     * @param array{');
        for (const line of renderRequestArrayShapeLines(schema, schemaRegistry)) {
            docLines.push(`     *     ${line}`);
        }
        docLines.push('     * } $data');
    }

    if (returnDoc) {
        docLines.push('     *');
        docLines.push(`     * @return ${returnDoc}`);
    }

    docLines.push('     *');
    docLines.push('     * @throws HyvorApiException');

    const pathExpr = wrapPath(buildPathExpr(endpoint.suffix, endpoint.pathParams));
    const requestCall = `${caller.requestReceiver}->request('${endpoint.httpMethod}', ${pathExpr}, ${dataExpr}, $options)`;

    const bodyLines: string[] = [];
    if (endpoint.response.shape === 'void') {
        bodyLines.push(`        ${requestCall};`);
    } else if (endpoint.response.shape === 'raw') {
        bodyLines.push(`        return ${requestCall};`);
    } else {
        bodyLines.push(`        $result = ${requestCall};`);
        bodyLines.push('');
        if (endpoint.response.shape === 'object') {
            bodyLines.push(`        return ${caller.transportReceiver}->denormalize($result, ${returnType}::class);`);
        } else {
            const itemShort = returnDoc!.slice(0, -2); // strip trailing "[]"
            bodyLines.push(`        return ${caller.transportReceiver}->denormalizeList($result, ${itemShort}::class);`);
        }
    }

    return [
        '    /**',
        ...docLines,
        '     */',
        `    public function ${name}(${params}): ${returnType}`,
        '    {',
        ...bodyLines,
        '    }',
    ].join('\n');
}
