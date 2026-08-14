import { generatePhp } from './php.ts';
import { Product } from './schema.ts';

// usage: tsx generate.ts [outputRoot] [product,product,...]
// ex: tsx generate.ts ../php/packages/talk/src talk
const outputRoot = process.argv[2];
const products = process.argv[3]?.split(',') as Product[] | undefined;

generatePhp(outputRoot, products);
