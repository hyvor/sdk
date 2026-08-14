// The npm package name of the hand-authored shared core (Transport, auth,
// exceptions, RequestOptions, ...) that every generated product package
// depends on - the TS equivalent of PHP's `hyvor/sdk-core`. Generated code
// imports from it by this bare specifier, never by relative path.
export const CORE_PACKAGE = '@hyvor/sdk-core';

export function core(exportName: string): { packageName: string, exportName: string } {
    return { packageName: CORE_PACKAGE, exportName };
}
