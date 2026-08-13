// Tracks a PHP file's namespace and `use` imports so generation code can
// reference a class by FQCN and get back the short name to write, without
// duplicating imports or writing on-the-nose `use` statements for
// same-namespace classes.
export class PhpFile {

    private uses = new Map<string, string>(); // shortName => FQCN

    constructor(public readonly namespace: string) {
    }

    /**
     * Registers `fqcn` for use in this file and returns the short name to
     * reference it by in code.
     */
    use(fqcn: string): string {
        const shortName = fqcn.split('\\').pop()!;
        const ownFqcn = `${this.namespace}\\${shortName}`;

        if (fqcn === ownFqcn) {
            return shortName;
        }

        const existing = this.uses.get(shortName);
        if (existing !== undefined && existing !== fqcn) {
            throw new Error(
                `Import collision in namespace ${this.namespace}: ` +
                `"${shortName}" refers to both ${existing} and ${fqcn}`,
            );
        }

        this.uses.set(shortName, fqcn);
        return shortName;
    }

    private renderUses(): string {
        return Array.from(this.uses.values())
            .sort((a, b) => a.localeCompare(b))
            .map(fqcn => `use ${fqcn};`)
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
