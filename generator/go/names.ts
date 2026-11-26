// Words Go style guides want fully upper-cased in an identifier (ex: "api_key"
// => "APIKey", "body_html" => "BodyHTML").
const INITIALISMS = new Set([
    'api', 'id', 'ip', 'url', 'uri', 'uuid', 'html', 'http', 'https', 'json', 'sso', 'dns', 'smtp', 'tls',
    'ssl', 'ttl', 'utc', 'xml', 'csv', 'cpu', 'sql', 'ui', 'dkim', 'spf', 'dmarc',
]);

const GO_KEYWORDS = new Set([
    'break', 'case', 'chan', 'const', 'continue', 'default', 'defer', 'else', 'fallthrough', 'for', 'func',
    'go', 'goto', 'if', 'import', 'interface', 'map', 'package', 'range', 'return', 'select', 'struct',
    'switch', 'type', 'var',
    // predeclared identifiers worth not shadowing in generated code
    'any', 'error', 'string', 'int', 'bool', 'nil', 'len', 'ctx', 'opts', 'data', 'core', 'fmt', 'url',
]);

function splitWords(word: string): string[] {
    return word
        // "listSkipResubscribeOn" => "list Skip Resubscribe On"
        .replace(/([a-z0-9])([A-Z])/g, '$1 $2')
        .split(/[^a-zA-Z0-9]+/)
        .filter(Boolean);
}

/**
 * An exported Go identifier from a snake_case / camelCase / dotted word,
 * applying Go initialism casing (ex: "created_at" => "CreatedAt", "list_ids"
 * => "ListIDs", "api_keys" => "APIKeys").
 */
export function goExported(word: string): string {
    const ident = splitWords(word)
        .map(part => {
            const lower = part.toLowerCase();
            if (INITIALISMS.has(lower)) return lower.toUpperCase();
            // plural initialisms: "ids" => "IDs", "urls" => "URLs"
            if (lower.endsWith('s') && INITIALISMS.has(lower.slice(0, -1))) {
                return lower.slice(0, -1).toUpperCase() + 's';
            }
            return part.charAt(0).toUpperCase() + part.slice(1);
        })
        .join('');

    return /^[0-9]/.test(ident) ? `X${ident}` : ident;
}

/** An unexported (lowerCamel) Go identifier, safe from keywords/predeclared names. */
export function goParamName(word: string): string {
    const words = splitWords(word);
    const camel = words
        .map((part, i) => i === 0 ? part.charAt(0).toLowerCase() + part.slice(1) : part.charAt(0).toUpperCase() + part.slice(1))
        .join('');
    const ident = /^[0-9]/.test(camel) ? `x${camel}` : camel;

    return GO_KEYWORDS.has(ident) ? `${ident}_` : ident;
}

/** `// ` comment lines for a (possibly multi-line) description. */
export function docLines(text: string, prefix = ''): string[] {
    return text.trim().split(/\r?\n/).map(line => `${prefix}// ${line}`.trimEnd());
}
