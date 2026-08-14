import path from 'path';
import { Product } from './schema.ts';

export type GenerateMode = 'default' | 'tmp';
export type GeneratorLanguage = 'php' | 'ts';

// Where each language's packages live, relative to this directory (generator/).
const LANGUAGE_PACKAGE_DIRS: Record<GeneratorLanguage, string> = {
    php: '../php',
    ts: '../js',
};

// generator/tmp/{php,js} - named after each language's own package dir, for consistency.
const TMP_DIRS: Record<GeneratorLanguage, string> = {
    php: 'tmp/php',
    ts: 'tmp/js',
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

    return path.join(LANGUAGE_PACKAGE_DIRS[language], 'packages', product, 'src');
}
