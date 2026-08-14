import fs from 'fs';
import path from 'path';
import { camelCase, pascalCase } from './helpers.ts';
import { getSchemas, PRODUCT_CONFIG, Product } from './schema.ts';
import { buildDtoImportMap, generateDtoFile } from './ts/dto.ts';
import { DtoImportMap } from './ts/types.ts';
import { generateOrgFiles, generateResourceFiles } from './ts/resources.ts';
import { generateProductClientFile } from './ts/clientTemplate.ts';

export function generateTs(outputRoot: string = 'tmp', products?: Product[]) {
    const schemas = getSchemas();
    const entries = (Object.entries(schemas) as [Product, typeof schemas[Product]][])
        .filter(([product]) => !products || products.includes(product));

    for (const [product, processed] of entries) {
        const productPascal = pascalCase(product);
        const outDir = path.join(outputRoot, productPascal);
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
            generateIndexFile(dtoImports, clientClassName, resourceClassName),
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
 * type (so callers can type their own variables/functions against them),
 * plus the product client and its main resource client class.
 * Sub-resource classes (ex: `DomainsResource`) aren't re-exported here:
 * they're only ever reached through a resource client's property, never
 * constructed or referenced by name directly.
 *
 * A DTO can share its bare name with the resource client class (ex: Post's
 * `Newsletter` response object vs. the `Newsletter` resource client -
 * disambiguated internally the same way `types.ts`'s `resolveRef` aliases
 * it within `Newsletter.ts` itself): named individually (rather than
 * `export * from './Dto.js'`) so that one can be re-exported under
 * `{Name}Dto` instead.
 */
function generateIndexFile(
    dtoImports: DtoImportMap,
    clientClassName: string,
    resourceClassName: string,
): { relativePath: string, content: string } {
    const dtoExportNames = Array.from(new Set(Object.values(dtoImports).map(target => target.exportName)));

    const dtoExports = dtoExportNames
        .sort((a, b) => a.localeCompare(b))
        .map(name => name === resourceClassName
            ? `export { ${name} as ${name}Dto } from './Dto.js';`
            : `export { ${name} } from './Dto.js';`);

    const lines = [
        ...dtoExports,
        `export * from './${clientClassName}.js';`,
        `export * from './Org.js';`,
        `export * from './${resourceClassName}.js';`,
        '',
    ];

    return { relativePath: 'index.ts', content: lines.join('\n') };
}
