import { OpenAPIV3 } from 'openapi-types';
import { Endpoint } from '../helpers.ts';
import { PhpFile } from './write.ts';
import { DtoImportMap } from './types.ts';
import { renderRequestArrayShapeLines } from './requestShape.ts';

const HTTP_METHOD_TO_NAME: Record<string, string> = {
    POST: 'create',
    PUT: 'update',
    PATCH: 'update',
    DELETE: 'delete',
    GET: 'get',
};

function methodName(httpMethod: string): string {
    return HTTP_METHOD_TO_NAME[httpMethod] ?? httpMethod.toLowerCase();
}

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

export function renderMethod(
    endpoint: Endpoint,
    docPath: string,
    pathExpr: string,
    caller: CallerContext,
    file: PhpFile,
    dtoImports: DtoImportMap,
    requestSchemas: Record<string, OpenAPIV3.SchemaObject>,
): string {
    const name = methodName(endpoint.method);
    const hasData = endpoint.requestSchemaName !== null;
    const dataExpr = hasData ? '$data' : 'null';

    const params = [
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
    }

    const docLines = [`     * ${endpoint.method} ${docPath}`];

    if (hasData) {
        const schema = requestSchemas[endpoint.requestSchemaName!];
        docLines.push('     *');
        docLines.push('     * @param array{');
        for (const line of renderRequestArrayShapeLines(schema)) {
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

    const requestCall = `${caller.requestReceiver}->request('${endpoint.method}', ${pathExpr}, ${dataExpr}, $options)`;

    const bodyLines: string[] = [];
    if (endpoint.response.shape === 'void') {
        bodyLines.push(`        ${requestCall};`);
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
