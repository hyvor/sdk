import fs from 'fs';
import path from 'path';
import { camelCase, pascalCase } from './helpers.ts';
import { GenerateMode, resolveOutDir } from './outputRoot.ts';
import { getSchemas, PRODUCT_CONFIG, Product } from './schema.ts';
import { buildDtoImportMap, generateDtoFile } from './ts/dto.ts';
import { generateOrgFiles, generateResourceFiles } from './ts/resources.ts';
import { generateProductClientFile } from './ts/clientTemplate.ts';

export function generateTs(mode: GenerateMode, products?: Product[]) {
    const schemas = getSchemas();
    const entries = (Object.entries(schemas) as [Product, typeof schemas[Product]][])
        .filter(([product]) => !products || products.includes(product));

    for (const [product, processed] of entries) {
        const productPascal = pascalCase(product);
        const productRoot = resolveOutDir('ts', product, mode);
        // Unlike PHP, a real TS package (../js/packages/{product}/src) needs
        // no extra product-name nesting - the npm package name already
        // scopes it. The nesting only earns its keep under 'tmp', where
        // every product in the language shares one scratch root.
        const outDir = mode === 'tmp' ? path.join(productRoot, productPascal) : productRoot;
        const config = PRODUCT_CONFIG[product];

        fs.rmSync(outDir, { recursive: true, force: true });
        fs.mkdirSync(outDir, { recursive: true });

        const dtoImports = buildDtoImportMap(processed.responses, processed.requests);
        const orgExampleGroup = processed.endpoints.org[0]?.name ?? null;

        const clientClassName = `${productPascal}Client`;
        const resourceClassName = pascalCase(processed.selfGroupName);

        const files = [
            generateDtoFile(processed.responses, processed.requests, dtoImports),
            ...generateOrgFiles(processed, dtoImports),
            ...generateResourceFiles(processed, dtoImports, config),
            generateProductClientFile(product, processed.selfGroupName, orgExampleGroup ? camelCase(orgExampleGroup) : null),
            generateIndexFile(clientClassName, resourceClassName),
        ];

        for (const file of files) {
            const fullPath = path.join(outDir, file.relativePath);
            fs.mkdirSync(path.dirname(fullPath), { recursive: true });
            fs.writeFileSync(fullPath, file.content);
        }

        console.log(`Generated ${files.length} files for "${product}" -> ${outDir}`);
    }
}

/**
 * `index.ts` - the package's public entry point: every DTO/request-Input
 * type, namespaced under `Dto` (ex: `import { Dto } from '@hyvor/sdk-talk';
 * function f(x: Dto.Website) {...}` - also works for the real enum values,
 * ex: `Dto.UserRole.ADMIN`, since `Dto.ts` mixes interfaces and enums), plus
 * the product client and its main resource client class. Sub-resource
 * classes (ex: `DomainsResource`) aren't re-exported here: they're only
 * ever reached through a resource client's property, never constructed or
 * referenced by name directly.
 *
 * Namespacing the DTOs this way, rather than flattening every name into the
 * package's top-level export, is also what sidesteps a DTO sharing its bare
 * name with the resource client class (ex: Post's `Newsletter` response
 * object vs. the `Newsletter` resource client, itself disambiguated
 * internally by `types.ts`'s `resolveRef` aliasing it within `Newsletter.ts`)
 * - `Dto.Newsletter` and the top-level `Newsletter` simply don't collide.
 */
function generateIndexFile(
    clientClassName: string,
    resourceClassName: string,
): { relativePath: string, content: string } {
    const lines = [
        `export * as Dto from './Dto.js';`,
        `export * from './${clientClassName}.js';`,
        `export * from './Org.js';`,
        `export * from './${resourceClassName}.js';`,
        '',
    ];

    return { relativePath: 'index.ts', content: lines.join('\n') };
}
