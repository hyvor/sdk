export interface ApiKey {
    id: number;
    name: string;
    scopes: string[] | Record<string, string>;
    key: string | null;
    created_at: number;
    is_enabled: boolean;
    last_accessed_at: number | null;
}
