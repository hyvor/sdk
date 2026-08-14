export function pascalCase(word: string): string {
    return word
        .split(/[^a-zA-Z0-9]+/)
        .filter(Boolean)
        .map(part => part.charAt(0).toUpperCase() + part.slice(1))
        .join('');
}

// ex: "api_key" => "apiKey" - used by the TS generator for property names
// (group tags are snake_case in the OpenAPI specs; TS convention is camelCase).
export function camelCase(word: string): string {
    const pascal = pascalCase(word);
    return pascal.charAt(0).toLowerCase() + pascal.slice(1);
}
