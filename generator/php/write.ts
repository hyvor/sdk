// Tracks a PHP file's namespace and `use` imports so generation code can
// reference a class by FQCN and get back the short name to write, without
// duplicating imports or writing on-the-nose `use` statements for
// same-namespace classes.
export class PhpFile {

    private uses = new Map<string, string>(); // alias (usually the short name) => FQCN
    private declaredClassName: string | null = null;

    constructor(public readonly namespace: string) {
    }

    /**
     * Registers the name of the class/enum this file declares, so `use()`
     * can tell an import apart from a name collision with it (ex: a
     * `Newsletter` resource client importing the unrelated `Newsletter` DTO
     * that shares its short name).
     */
    declareClass(name: string): void {
        this.declaredClassName = name;
    }

    /**
     * Registers `fqcn` for use in this file and returns the name to
     * reference it by in code - normally its short name, but an
     * automatically disambiguated alias if that short name is already taken
     * by this file's own class or a different, already-imported FQCN.
     */
    use(fqcn: string): string {
        const parts = fqcn.split('\\');
        const shortName = parts[parts.length - 1];
        const ownFqcn = `${this.namespace}\\${shortName}`;

        if (fqcn === ownFqcn) {
            return shortName;
        }

        for (const [alias, existingFqcn] of this.uses) {
            if (existingFqcn === fqcn) {
                return alias;
            }
        }

        const parentNamespace = parts[parts.length - 2] ?? '';
        let alias = shortName;
        let suffix = 2;

        while (alias === this.declaredClassName || this.uses.has(alias)) {
            alias = suffix === 2 ? `${parentNamespace}${shortName}` : `${parentNamespace}${shortName}${suffix}`;
            suffix++;
        }

        this.uses.set(alias, fqcn);
        return alias;
    }

    private renderUses(): string {
        return Array.from(this.uses.entries())
            .sort(([, a], [, b]) => a.localeCompare(b))
            .map(([alias, fqcn]) => {
                const shortName = fqcn.split('\\').pop()!;
                return alias === shortName ? `use ${fqcn};` : `use ${fqcn} as ${alias};`;
            })
            .join('\n');
    }

    render(body: string): string {
        const uses = this.renderUses();

        return [
            '<?php',
            '',
            'declare(strict_types=1);',
            '',
            `namespace ${this.namespace};`,
            '',
            ...(uses ? [uses, ''] : []),
            body.trimEnd(),
            '',
        ].join('\n');
    }

}
