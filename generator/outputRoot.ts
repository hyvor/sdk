import path from 'path';
import { Product } from './schema.ts';

export type GenerateMode = 'default' | 'tmp';
export type GeneratorLanguage = 'php' | 'ts' | 'go';

// Where each language's packages live, relative to this directory (generator/).
const LANGUAGE_PACKAGE_DIRS: Record<GeneratorLanguage, string> = {
    php: '../php',
    ts: '../js',
    go: '../go',
};

// generator/tmp/{php,js} - named after each language's own package dir, for consistency.
const TMP_DIRS: Record<GeneratorLanguage, string> = {
    php: 'tmp/php',
    ts: 'tmp/js',
    go: 'tmp/go',
};

/**
 * Resolves where a product's generated files are written. Only two
 * locations are supported (no arbitrary output paths):
 *  - 'default': straight into that product's real package `src/` (ex:
 *    "../php/packages/talk/src", "../js/packages/talk/src") - what actually
 *    ships.
 *  - 'tmp': into generator/tmp/{php,js}, a scratch root shared by every
 *    product generated in that language, so nothing under a real package is
 *    touched until the output has been reviewed.
 */
export function resolveOutDir(language: GeneratorLanguage, product: Product, mode: GenerateMode): string {
    if (mode === 'tmp') {
        return TMP_DIRS[language];
    }

    // Go has no packages/{product}/src nesting: a product is one Go package,
    // a plain directory of the module (../go/talk).
    if (language === 'go') {
        return path.join(LANGUAGE_PACKAGE_DIRS.go, product);
    }

    return path.join(LANGUAGE_PACKAGE_DIRS[language], 'packages', product, 'src');
}
