import fs from 'fs';
import path from 'path';
import { pascalCase } from './helpers.ts';
import { getSchemas, PRODUCT_CONFIG, Product } from './schema.ts';
import { buildDtoImportMap, generateDtoFiles } from './php/dto.ts';
import { generateOrgFiles, generateResourceFiles } from './php/resources.ts';
import { generateProductClientFile } from './php/clientTemplate.ts';

export function generatePhp(outputRoot: string = 'tmp', products?: Product[]) {
    const schemas = getSchemas();
    const entries = (Object.entries(schemas) as [Product, typeof schemas[Product]][])
        .filter(([product]) => !products || products.includes(product));

    for (const [product, processed] of entries) {
        const productNamespace = pascalCase(product);
        const namespaceBase = `Hyvor\\Sdk\\${productNamespace}`;
        const outDir = path.join(outputRoot, productNamespace);
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
