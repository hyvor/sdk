import path from 'path';

// An import target within the same package - `modulePath` is a POSIX path,
// relative to the package's `src/` root, with no extension (ex:
// "Website/DomainsResource"). The relative `./...` specifier written into
// the output is derived from this and the rendering file's own modulePath.
export interface InternalTarget {
    modulePath: string;
    exportName: string;
}

// An import target from another published package (ex: `@hyvor/sdk-core`),
// imported by its bare package specifier rather than a relative path.
export interface ExternalTarget {
    packageName: string;
    exportName: string;
}

export type ImportTarget = InternalTarget | ExternalTarget;

function sourceKey(target: ImportTarget): string {
    return 'modulePath' in target ? `internal:${target.modulePath}` : `external:${target.packageName}`;
}

function fromSpecifier(target: ImportTarget, ownModulePath: string): string {
    if ('packageName' in target) {
        return target.packageName;
    }

    // Computed from dirname/basename, not `path.relative(dirname(own),
    // target.modulePath)` directly: a target modulePath can share its full
    // string with another file's *directory* (ex: file "Website" vs
    // directory "Website/" holding "Website/DomainsResource") - treating
    // modulePath as a pseudo-directory in that case collapses the relative
    // path to "". Splitting into dir+base first keeps the file/directory
    // namespaces distinct.
    const ownDir = path.posix.dirname(ownModulePath);
    const targetDir = path.posix.dirname(target.modulePath);
    const targetBase = path.posix.basename(target.modulePath);

    const relDir = path.posix.relative(ownDir, targetDir);
    const specifier = relDir === '' ? targetBase : `${relDir}/${targetBase}`;

    return (specifier.startsWith('.') ? specifier : `./${specifier}`) + '.js';
}

// Tracks a generated TS module's imports so generation code can reference an
// export - either from elsewhere in the same package (an `InternalTarget`)
// or from a dependency package like `@hyvor/sdk-core` (an `ExternalTarget`)
// - and get back the local name to write, without duplicating imports or
// hand-computing relative paths.
export class TsFile {

    // alias -> { target, isType }
    private imports = new Map<string, { target: ImportTarget, isType: boolean }>();
    private declaredName: string | null = null;

    constructor(public readonly modulePath: string) {
    }

    /**
     * Registers the name of the class/interface/enum this file's primary
     * export is declared under, so `use()` can tell an import apart from a
     * name collision with it.
     */
    declareExport(name: string): void {
        this.declaredName = name;
    }

    /**
     * Registers `target` for use in this file and returns the name to
     * reference it by in code - normally its export name, but an
     * automatically disambiguated alias on collision. `kind: 'value'` marks
     * a real runtime reference (ex: `new Website(...)`, `extends
     * HyvorBaseClient`); `'type'` (the default) marks a type-only reference,
     * which is elided from emitted JS.
     */
    use(target: ImportTarget, kind: 'type' | 'value' = 'type'): string {
        if ('modulePath' in target && target.modulePath === this.modulePath) {
            return target.exportName;
        }

        const key = sourceKey(target);

        for (const [alias, existing] of this.imports) {
            if (sourceKey(existing.target) === key && existing.target.exportName === target.exportName) {
                if (kind === 'value' && existing.isType) {
                    existing.isType = false;
                }
                return alias;
            }
        }

        let alias = target.exportName;
        let suffix = 2;
        while (alias === this.declaredName || this.imports.has(alias)) {
            alias = `${target.exportName}${suffix}`;
            suffix++;
        }

        this.imports.set(alias, { target, isType: kind === 'type' });
        return alias;
    }

    private renderImports(): string {
        const bySource = new Map<string, { target: ImportTarget, items: Array<{ alias: string, exportName: string, isType: boolean }> }>();

        for (const [alias, { target, isType }] of this.imports) {
            const key = sourceKey(target);
            const group = bySource.get(key) ?? { target, items: [] };
            group.items.push({ alias, exportName: target.exportName, isType });
            bySource.set(key, group);
        }

        return Array.from(bySource.values())
            .sort((a, b) => fromSpecifier(a.target, this.modulePath).localeCompare(fromSpecifier(b.target, this.modulePath)))
            .map(({ target, items }) => {
                const sorted = items.slice().sort((a, b) => a.exportName.localeCompare(b.exportName));
                const allTypes = sorted.every(item => item.isType);
                const specifiers = sorted
                    .map(item => {
                        const named = item.alias === item.exportName ? item.exportName : `${item.exportName} as ${item.alias}`;
                        return !allTypes && item.isType ? `type ${named}` : named;
                    })
                    .join(', ');
                const typePrefix = allTypes ? 'type ' : '';
                return `import ${typePrefix}{ ${specifiers} } from '${fromSpecifier(target, this.modulePath)}';`;
            })
            .join('\n');
    }

    render(body: string): string {
        const imports = this.renderImports();

        return [
            ...(imports ? [imports, ''] : []),
            body.trimEnd(),
            '',
        ].join('\n');
    }

}
