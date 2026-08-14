import fs from 'fs';
import path from 'path';
import { camelCase, pascalCase } from './helpers.ts';
import { getSchemas, PRODUCT_CONFIG, Product } from './schema.ts';
import { buildDtoImportMap, generateRequestDtoFiles, generateResponseDtoFiles } from './ts/dto.ts';
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

        const dtoFiles = [
            ...generateResponseDtoFiles(processed.responses, dtoImports),
            ...generateRequestDtoFiles(processed.requests, dtoImports),
        ];
        const clientClassName = `${productPascal}Client`;
        const resourceClassName = pascalCase(processed.selfGroupName);

        const files = [
            ...dtoFiles,
            ...generateOrgFiles(processed, dtoImports),
            ...generateResourceFiles(processed, dtoImports, config),
            generateProductClientFile(product, processed.selfGroupName, orgExampleGroup ? camelCase(orgExampleGroup) : null),
            generateIndexFile(dtoFiles, clientClassName, resourceClassName),
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
 * disambiguated internally the same way PHP's generated `Newsletter.php`
 * imports its DTO counterpart aliased, see `types.ts`'s `resolveRef`): `export
 * *` can't alias, so that one DTO is re-exported by name instead, as
 * `{Name}Dto`.
 */
function generateIndexFile(
    dtoFiles: { relativePath: string }[],
    clientClassName: string,
    resourceClassName: string,
): { relativePath: string, content: string } {
    const dtoExports = dtoFiles
        .map(file => file.relativePath.replace(/\.ts$/, ''))
        .sort((a, b) => a.localeCompare(b))
        .map(modulePath => {
            const exportName = modulePath.split('/').pop()!;
            return exportName === resourceClassName
                ? `export { ${exportName} as ${exportName}Dto } from './${modulePath}.js';`
                : `export * from './${modulePath}.js';`;
        });

    const lines = [
        ...dtoExports,
        `export * from './${clientClassName}.js';`,
        `export * from './Org.js';`,
        `export * from './${resourceClassName}.js';`,
        '',
    ];

    return { relativePath: 'index.ts', content: lines.join('\n') };
}
