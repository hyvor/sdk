import { Endpoint, PathParam } from '../schema.ts';
import { docLines, goExported, goParamName } from './names.ts';
import { DtoNames } from './types.ts';
import { CORE_IMPORT, GoFile } from './write.ts';

/**
 * How a generated method reaches the transport. `request` is the call
 * expression receiving (ctx, method, path, body, opts): `c.request` on a
 * resource client's own methods, `r.client.request` on a sub-resource, and
 * `r.transport.Request` on an org resource.
 */
export interface CallerContext {
    receiver: string; // ex: "r *WebsiteDomainsResource"
    request: string;
}

function goPathParamType(param: PathParam): string {
    switch (param.phpType) {
        case 'int': return 'int';
        case 'float': return 'float64';
        case 'bool': return 'bool';
        default: return 'string';
    }
}

/**
 * Builds the Go expression for an endpoint's request path: a plain quoted
 * literal when it has no path params, or a `fmt.Sprintf` interpolating each
 * one otherwise. String params are `url.PathEscape`d, since they can't be
 * trusted to be URL-safe as-is (ex: an email).
 */
function buildPathExpr(suffix: string, pathParams: PathParam[], file: GoFile): string {
    if (pathParams.length === 0) {
        return JSON.stringify(suffix);
    }

    const byName = new Map(pathParams.map(param => [param.name, param]));
    const args: string[] = [];
    const format = suffix.replace(/%/g, '%%').replace(/\{(\w+)\}/g, (_, name: string) => {
        const param = byName.get(name);
        if (!param) {
            throw new Error(`Path param "{${name}}" in "${suffix}" has no matching parameter.`);
        }

        const ident = goParamName(param.name);
        switch (goPathParamType(param)) {
            case 'int': args.push(ident); return '%d';
            case 'float64': args.push(ident); return '%v';
            case 'bool': args.push(ident); return '%t';
            default: args.push(`${file.use('net/url')}.PathEscape(${ident})`); return '%s';
        }
    });

    return `${file.use('fmt')}.Sprintf(${JSON.stringify(format)}, ${args.join(', ')})`;
}

export function renderMethod(
    endpoint: Endpoint,
    wrapPath: (pathExpr: string) => string,
    caller: CallerContext,
    file: GoFile,
    names: DtoNames,
): string {
    const name = goExported(endpoint.methodName);
    const hasData = endpoint.requestSchemaName !== null;
    const core = file.use(CORE_IMPORT);
    const context = file.use('context');

    const params = [
        `ctx ${context}.Context`,
        ...endpoint.pathParams.map(param => `${goParamName(param.name)} ${goPathParamType(param)}`),
        ...(hasData ? [`data ${names[endpoint.requestSchemaName!]}`] : []),
        `opts ...${core}.RequestOption`,
    ].join(', ');

    const className = endpoint.response.className ? names[endpoint.response.className] : null;
    let returnType: string;
    let decode: string | null;

    switch (endpoint.response.shape) {
        case 'object':
            returnType = `(*${className}, error)`;
            decode = `${core}.Decode[${className}](raw)`;
            break;
        case 'list':
            returnType = `([]${className}, error)`;
            decode = `${core}.DecodeList[${className}](raw)`;
            break;
        case 'raw':
            returnType = '(any, error)';
            decode = `${core}.DecodeRaw(raw)`;
            break;
        default:
            returnType = 'error';
            decode = null;
    }

    const pathExpr = wrapPath(buildPathExpr(endpoint.suffix, endpoint.pathParams, file));
    const call = `${caller.request}(ctx, ${JSON.stringify(endpoint.httpMethod)}, ${pathExpr}, ${hasData ? 'data' : 'nil'}, opts)`;

    const body = decode === null
        ? [`\t_, err := ${call}`, '\treturn err']
        : [
            `\traw, err := ${call}`,
            '\tif err != nil {',
            '\t\treturn nil, err',
            '\t}',
            `\treturn ${decode}`,
        ];

    const summary = (endpoint.summary ?? endpoint.description)?.trim();
    const doc = summary
        ? [...docLines(`${name}: ${summary}`), '//', `// ${endpoint.httpMethod} ${endpoint.suffix}`]
        : [`// ${name} calls ${endpoint.httpMethod} ${endpoint.suffix}.`];

    return [
        ...doc,
        `func (${caller.receiver}) ${name}(${params}) ${returnType} {`,
        ...body,
        '}',
    ].join('\n');
}
