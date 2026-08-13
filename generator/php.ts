import fs from 'fs';
import path from 'path';
import { pascalCase } from './helpers.ts';
import { getSchemas } from './schema.ts';
import { buildDtoImportMap, generateDtoFiles } from './php/dto.ts';
import { generateOrgFiles, generateWebsiteFiles } from './php/resources.ts';
import { generateProductClientFile } from './php/clientTemplate.ts';

export function generatePhp(outputRoot: string = 'tmp') {
    const schemas = getSchemas();

    for (const [product, processed] of Object.entries(schemas)) {
        const productNamespace = pascalCase(product);
        const namespaceBase = `Hyvor\\Sdk\\${productNamespace}`;
        const outDir = path.join(outputRoot, productNamespace);

        fs.rmSync(outDir, { recursive: true, force: true });
        fs.mkdirSync(outDir, { recursive: true });

        const dtoImports = buildDtoImportMap(processed.responses, namespaceBase);

        const files = [
            ...generateDtoFiles(processed.responses, dtoImports, namespaceBase),
            ...generateOrgFiles(processed, namespaceBase, dtoImports),
            ...generateWebsiteFiles(processed, namespaceBase, dtoImports),
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
