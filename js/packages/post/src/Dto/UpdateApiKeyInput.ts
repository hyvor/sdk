export interface UpdateApiKeyInput {
    name: string;
    is_enabled: boolean;
    scopes: string[] | Record<string, string>;
}
