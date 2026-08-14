import { Endpoint, PathParam } from '../schema.ts';
import { TsFile } from './write.ts';
import { DtoImportMap } from './types.ts';
import { core } from './core.ts';

/**
 * Where a method sends its request through and denormalizes its response
 * through - both are `this.transport` for org resources and methods
 * declared directly on the resource client (Website), but
 * `this.client`/`this.client.transport` for a sub-resource that only holds
 * a reference to its owning resource client.
 */
export interface CallerContext {
    requestReceiver: string;
    transportReceiver: string;
}

export const TRANSPORT_CALLER: CallerContext = {
    requestReceiver: 'this.transport',
    transportReceiver: 'this.transport',
};

export const CLIENT_CALLER: CallerContext = {
    requestReceiver: 'this.client',
    transportReceiver: 'this.client.transport',
};

// for methods declared directly on the resource client (ex: Website's own
// `delete()`): requests must still go through its own `request()` so
// apiKey/headers get applied, but denormalization can talk to the transport
// directly.
export const SELF_CALLER: CallerContext = {
    requestReceiver: 'this',
    transportReceiver: 'this.transport',
};

function pathParamType(param: PathParam): string {
    return param.phpType === 'int' ? 'number' : 'string';
}

/**
 * Builds the TS expression for an endpoint's request path: a plain quoted
 * literal when it has no path params, or a template literal interpolating
 * each one otherwise (ex: `` `/issues/${id}/send` ``). Non-numeric params
 * (ex: an email) are `encodeURIComponent()`-wrapped, since they can't be
 * trusted to be URL-safe as-is.
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
        parts.push(suffix.slice(lastIndex, match.index));

        const param = paramsByName.get(match[1]);
        if (!param) {
            throw new Error(`Path param "{${match[1]}}" in "${suffix}" has no matching parameter.`);
        }

        parts.push(param.phpType === 'int' ? `\${${param.name}}` : `\${encodeURIComponent(${param.name})}`);
        lastIndex = placeholderPattern.lastIndex;
    }

    parts.push(suffix.slice(lastIndex));

    return '`' + parts.join('') + '`';
}

function useDto(modulePath: string, file: TsFile): string {
    const exportName = modulePath.split('/').pop()!;
    return file.use({ modulePath, exportName }, 'type');
}

export function renderMethod(
    endpoint: Endpoint,
    docPath: string,
    wrapPath: (pathExpr: string) => string,
    caller: CallerContext,
    file: TsFile,
    dtoImports: DtoImportMap,
): string {
    const name = endpoint.methodName;
    const hasData = endpoint.requestSchemaName !== null;

    const requestOptionsType = file.use(core('RequestOptions'), 'type');
    const params = [
        ...endpoint.pathParams.map(param => `${param.name}: ${pathParamType(param)}`),
        ...(hasData ? [`data: ${useDto(dtoImports[endpoint.requestSchemaName!], file)}`] : []),
        `options?: ${requestOptionsType}`,
    ].join(', ');

    let returnType = 'void';
    let denormalizeType: string | null = null;

    if (endpoint.response.shape === 'object' && endpoint.response.className) {
        returnType = useDto(dtoImports[endpoint.response.className], file);
        denormalizeType = returnType;
    } else if (endpoint.response.shape === 'list' && endpoint.response.className) {
        const itemType = useDto(dtoImports[endpoint.response.className], file);
        returnType = `${itemType}[]`;
        denormalizeType = itemType;
    } else if (endpoint.response.shape === 'raw') {
        returnType = 'unknown';
    }

    const pathExpr = wrapPath(buildPathExpr(endpoint.suffix, endpoint.pathParams));
    const dataExpr = hasData ? 'data' : 'null';
    const requestCall = `${caller.requestReceiver}.request('${endpoint.httpMethod}', ${pathExpr}, ${dataExpr}, options)`;

    const bodyLines: string[] = [];
    if (endpoint.response.shape === 'void') {
        bodyLines.push(`        await ${requestCall};`);
    } else if (endpoint.response.shape === 'raw') {
        bodyLines.push(`        return await ${requestCall};`);
    } else {
        bodyLines.push(`        const result = await ${requestCall};`);
        bodyLines.push('');
        if (endpoint.response.shape === 'object') {
            bodyLines.push(`        return ${caller.transportReceiver}.denormalize<${denormalizeType}>(result);`);
        } else {
            bodyLines.push(`        return ${caller.transportReceiver}.denormalizeList<${denormalizeType}>(result);`);
        }
    }

    return [
        '    /**',
        `     * ${endpoint.httpMethod} ${docPath}`,
        '     */',
        `    async ${name}(${params}): Promise<${returnType}> {`,
        ...bodyLines,
        '    }',
    ].join('\n');
}
