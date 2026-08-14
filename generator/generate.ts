import { generatePhp } from './php.ts';
import { generateTs } from './ts.ts';
import { GenerateMode } from './outputRoot.ts';
import { Product } from './schema.ts';

// usage: tsx generate.ts <php|ts|all> [product,product,...] [--tmp]
//
// Output location is fixed, not an arbitrary path:
//  - default: straight into the real package src (ex: ../php/packages/talk/src)
//  - --tmp:   into generator/tmp/{php,js}, for reviewing output before it touches a real package
//
// ex: tsx generate.ts php talk        # regenerate hyvor/sdk-talk (PHP) in place
//     tsx generate.ts ts --tmp        # preview every TS product under tmp/js
//     tsx generate.ts all             # regenerate every product, every language, in place
const USAGE = 'Usage: tsx generate.ts <php|ts|all> [product,product,...] [--tmp]';

const [, , language, ...rest] = process.argv;

if (language !== 'php' && language !== 'ts' && language !== 'all') {
    console.error(USAGE);
    process.exit(1);
}

const mode: GenerateMode = rest.includes('--tmp') ? 'tmp' : 'default';
const productsArg = rest.find(arg => arg !== '--tmp');
const products = productsArg?.split(',') as Product[] | undefined;

if (language === 'php' || language === 'all') {
    generatePhp(mode, products);
}
if (language === 'ts' || language === 'all') {
    generateTs(mode, products);
}
