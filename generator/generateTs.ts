import { generateTs } from './ts.ts';
import { Product } from './schema.ts';

// usage: tsx generateTs.ts [outputRoot] [product,product,...]
// ex: tsx generateTs.ts ../js/packages/talk/src talk
const outputRoot = process.argv[2];
const products = process.argv[3]?.split(',') as Product[] | undefined;

generateTs(outputRoot, products);
