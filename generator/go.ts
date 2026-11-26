import fs from 'fs';
import path from 'path';
import { GenerateMode, resolveOutDir } from './outputRoot.ts';
import { getSchemas, PRODUCT_CONFIG, Product } from './schema.ts';
import { buildDtoNames, generateDtoFile } from './go/dto.ts';
import { generateOrgFiles, generateResourceFiles, reservedTypeNames } from './go/resources.ts';
import { generateProductClientFile } from './go/clientTemplate.ts';
import { GENERATED_MARKER, GoFile } from './go/write.ts';

export function generateGo(mode: GenerateMode, products?: Product[]) {
    const schemas = getSchemas();
    const entries = (Object.entries(schemas) as [Product, typeof schemas[Product]][])
        .filter(([product]) => !products || products.includes(product));

    for (const [product, processed] of entries) {
        const productRoot = resolveOutDir('go', product, mode);
        // Under 'tmp', every product in the language shares one scratch root,
        // so each gets its own subdirectory. A real package dir is already
        // product-specific (../go/talk).
        const outDir = mode === 'tmp' ? path.join(productRoot, product) : productRoot;
        const config = PRODUCT_CONFIG[product];

        const names = buildDtoNames(processed.responses, processed.requests);
        assertNoNameCollisions(product, names, reservedTypeNames(processed));

        const newFile = () => new GoFile(product, product);
        const orgExampleGroup = processed.endpoints.org[0] ?? null;

        const files = [
            generateDtoFile(processed.responses, processed.requests, names, newFile()),
            ...generateOrgFiles(processed, names, newFile),
            ...generateResourceFiles(processed, names, config, newFile),
            generateProductClientFile(
                product,
                processed.selfGroupName,
                orgExampleGroup ? { group: orgExampleGroup.name, method: orgExampleGroup.endpoints[0].methodName } : null,
                newFile(),
            ),
        ];

        prepareOutDir(outDir, mode);

        for (const file of files) {
            fs.writeFileSync(path.join(outDir, file.relativePath), file.content);
        }

        console.log(`Generated ${files.length} files for "${product}" -> ${outDir}`);
    }
}

/**
 * Unlike PHP/TS, a Go package's hand-written tests (`*_test.go`) live in the
 * same directory as the generated code, so a real package dir can't be wiped
 * wholesale: only files carrying the `Code generated ... DO NOT EDIT` marker
 * are removed (this also cleans up files of since-removed groups). A tmp
 * dir is scratch space, and is wiped entirely.
 */
function prepareOutDir(outDir: string, mode: GenerateMode) {
    if (mode === 'tmp') {
        fs.rmSync(outDir, { recursive: true, force: true });
    }
    fs.mkdirSync(outDir, { recursive: true });

    for (const entry of fs.readdirSync(outDir)) {
        const full = path.join(outDir, entry);
        if (entry.endsWith('.go') && fs.readFileSync(full, 'utf-8').startsWith(GENERATED_MARKER)) {
            fs.rmSync(full);
        }
    }
}

function assertNoNameCollisions(product: string, dtoNames: Record<string, string>, reserved: string[]) {
    const seen = new Map<string, string>();

    for (const [schema, name] of Object.entries(dtoNames)) {
        const previous = seen.get(name);
        if (previous) {
            throw new Error(`${product}: schemas "${previous}" and "${schema}" both generate the Go type "${name}".`);
        }
        seen.set(name, schema);
    }

    for (const name of reserved) {
        const schema = seen.get(name);
        if (schema) {
            throw new Error(`${product}: schema "${schema}" generates the Go type "${name}", which collides with a generated client type.`);
        }
    }
}
