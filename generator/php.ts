import fs from 'fs';
import path from 'path';
import { pascalCase } from './helpers.ts';
import { GenerateMode, resolveOutDir } from './outputRoot.ts';
import { getSchemas, PRODUCT_CONFIG, Product } from './schema.ts';
import { buildDtoImportMap, generateDtoFiles } from './php/dto.ts';
import { generateOrgFiles, generateResourceFiles } from './php/resources.ts';
import { generateProductClientFile } from './php/clientTemplate.ts';

export function generatePhp(mode: GenerateMode, products?: Product[]) {
    const schemas = getSchemas();
    const entries = (Object.entries(schemas) as [Product, typeof schemas[Product]][])
        .filter(([product]) => !products || products.includes(product));

    for (const [product, processed] of entries) {
        const productNamespace = pascalCase(product);
        const namespaceBase = `Hyvor\\Sdk\\${productNamespace}`;
        // PHP always nests under the product's namespace folder, even in its
        // real package: every product shares one PSR-4 root (`Hyvor\Sdk\`),
        // so `packages/talk/src/Talk/...` is what makes `Hyvor\Sdk\Talk\...`
        // resolve - see php/packages/*/composer.json.
        const outDir = path.join(resolveOutDir('php', product, mode), productNamespace);
        const config = PRODUCT_CONFIG[product];

        fs.rmSync(outDir, { recursive: true, force: true });
        fs.mkdirSync(outDir, { recursive: true });

        const dtoImports = buildDtoImportMap(processed.responses, namespaceBase);

        const files = [
            ...generateDtoFiles(processed.responses, dtoImports, namespaceBase),
            ...generateOrgFiles(processed, namespaceBase, dtoImports),
            ...generateResourceFiles(processed, namespaceBase, dtoImports, config),
            generateProductClientFile(product, namespaceBase, processed.selfGroupName, processed.endpoints.org[0]?.name ?? null),
        ];

        for (const file of files) {
            const fullPath = path.join(outDir, file.relativePath);
            fs.mkdirSync(path.dirname(fullPath), { recursive: true });
            fs.writeFileSync(fullPath, file.content);
        }

        console.log(`Generated ${files.length} files for "${product}" -> ${outDir}`);
    }
}
